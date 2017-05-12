<?php

  class SummaryView implements IView {
    private $quarter_turnovers;
    private $quarters;
    private $scripts;
    private $turnover;
    private $year;

    public function __construct() {
      $this->loadData();
    }

    public function getScripts() {
      return $this->scripts;
    }

    public function getTitle() {
      return 'Summaries';
    }

    private function loadData() {
      $db       = Database::getDB();
      $con      = $db->getCon();

      $this->quarters = array();
      $this->quarter_turnovers = array();

      $sql = 'SELECT
                CASE
                  WHEN MONTH(`date`) IN (1, 2, 3) THEN 1
                  WHEN MONTH(`date`) IN (4, 5, 6) THEN 2
                  WHEN MONTH(`date`) IN (7, 8, 9) THEN 3
                  ELSE 4
                END AS `quarter`,
                `type`,
                `is_income`,
                ROUND(SUM(`netto`), 2) AS `netto_sum`,
                ROUND(SUM(`tax_7`), 2) AS `tax_7_sum`,
                ROUND(SUM(`tax_19`), 2) AS `tax_19_sum`,
                ROUND(SUM(`brutto`), 2) AS `brutto_sum`
              FROM
                `transactions`
              JOIN
                `categories`
                ON `categories`.`id` = `transactions`.`category_id`
              GROUP BY
                `type`,
                CASE
                  WHEN MONTH(`date`) IN (1, 2, 3) THEN 1
                  WHEN MONTH(`date`) IN (4, 5, 6) THEN 2
                  WHEN MONTH(`date`) IN (7, 8, 9) THEN 3
                  ELSE 4
                END
              ORDER BY
                `is_income` DESC,
                `type` ASC';

      $stmt   = $con->prepare($sql);
      $stmt->execute();
      $stmt->bind_result($quarter, $type, $is_income, $netto, $tax_7, $tax_19, $brutto);

      while ($stmt->fetch()) {
        if (!isset($this->quarters[$quarter])) {
          $this->quarters[$quarter] = array();
        }
        if (!isset($this->quarter_turnovers[$quarter])) {
          $this->quarter_turnovers[$quarter] = array(
            'netto' => 0, 'brutto' => 0,
            'tax_7' => 0, 'tax_19' => 0);
        }

        $this->quarters[$quarter][$type] = array(
          'is_income' => $is_income,
          'netto' => $netto,
          'tax_7' => $tax_7,
          'tax_19' => $tax_19,
          'brutto' => $brutto
        );

        if ($is_income) {
          $this->quarter_turnovers[$quarter]['netto'] += $netto;
          $this->quarter_turnovers[$quarter]['tax_7'] += $tax_7;
          $this->quarter_turnovers[$quarter]['tax_19'] += $tax_19;
          $this->quarter_turnovers[$quarter]['brutto'] += $brutto;

        } else {
          $this->quarter_turnovers[$quarter]['netto'] -= $netto;
          $this->quarter_turnovers[$quarter]['tax_7'] -= $tax_7;
          $this->quarter_turnovers[$quarter]['tax_19'] -= $tax_19;
          $this->quarter_turnovers[$quarter]['brutto'] -= $brutto;
        }
      }
      $stmt->close();




      $this->year = array();

      $sql = 'SELECT
                `type`,
                `is_income`,
                ROUND(SUM(`netto`), 2) AS `netto_sum`,
                ROUND(SUM(`tax_7`), 2) AS `tax_7_sum`,
                ROUND(SUM(`tax_19`), 2) AS `tax_19_sum`,
                ROUND(SUM(`brutto`), 2) AS `brutto_sum`
              FROM
                `transactions`
              JOIN
                `categories`
                ON `categories`.`id` = `transactions`.`category_id`
              GROUP BY
                `type`
              ORDER BY
                `is_income` DESC,
                `type` ASC';

      $stmt   = $con->prepare($sql);
      $stmt->execute();
      $stmt->bind_result($type, $is_income, $netto, $tax_7, $tax_19, $brutto);

      while ($stmt->fetch()) {
        if (!isset($this->year[$type])) {
          $this->year[$type] = array();
        }

        $this->year[$type] = array(
          'is_income' => $is_income,
          'netto' => $netto,
          'tax_7' => $tax_7,
          'tax_19' => $tax_19,
          'brutto' => $brutto
        );
      }
      $stmt->close();

      # gather turnover
      $this->turnover = array('netto' => 0, 'brutto' => 0,
                        'tax_7' => 0, 'tax_19' => 0);

      foreach ($this->year as $type => $values) {
        if ($values['is_income']) {
          foreach ($values as $key => $value) {
            if ($key == 'is_income') {
              continue;
            }

            $this->turnover[$key] += $value;
          }

        } else {
          foreach ($values as $key => $value) {
            if ($key == 'is_income') {
              continue;
            }

            $this->turnover[$key] -= $value;
          }
        }
      }

      $income_chart = new IncomeChart();
      $this->scripts .= $income_chart->getJavaScripts();

      $expense_chart = new ExpenseChart();
      $this->scripts .= $expense_chart->getJavaScripts();
    }

    private function printValueTable($types, $turnover) {
      echo '<table class="value_list">';
      echo '<thead>';
      echo '<tr>';
      echo '<td class="text">Category</td>';
      echo '<td class="number">Netto</td>';
      echo '<td class="number">7%</td>';
      echo '<td class="number">19%</td>';
      echo '<td class="number">Brutto</td>';
      echo '</tr>';
      echo '</thead>';

      echo '<tfoot>';
      echo '<tr class="turnover">';
      echo '<td></td>';
      echo '<td class="number '.($turnover['netto'] < 0 ? 'negative' : '').'">';
      echo sprintf('%01.2f €', $turnover['netto']);
      echo '</td>';
      echo '<td class="number '.($turnover['tax_7'] < 0 ? 'negative' : '').'">';
      echo sprintf('%01.2f €', $turnover['tax_7']);
      echo '</td>';
      echo '<td class="number '.($turnover['tax_19'] < 0 ? 'negative' : '').'">';
      echo sprintf('%01.2f €', $turnover['tax_19']);
      echo '</td>';
      echo '<td class="number '.($turnover['brutto'] < 0 ? 'negative' : '').'">';
      echo sprintf('%01.2f €', $turnover['brutto']);
      echo '</td>';
      echo '</tr>';
      echo '</tfoot>';

      echo '<tbody>';

      foreach ($types as $type => $value) {
        echo '<tr'.($value['is_income'] ? ' class="income"' : '').'>';
        echo '<td class="text">';
        echo $type;
        echo '</td>';
        echo '<td class="number">';
        echo sprintf('%01.2f €', $value['netto']);
        echo '</td>';
        echo '<td class="number">';
        echo sprintf('%01.2f €', $value['tax_7']);
        echo '</td>';
        echo '<td class="number">';
        echo sprintf('%01.2f €', $value['tax_19']);
        echo '</td>';
        echo '<td class="number">';
        echo sprintf('%01.2f €', $value['brutto']);
        echo '</td>';
        echo '</tr>';
      }

      echo '</tbody>';
      echo '</table>';
    }

    public function show() {
      echo '<h3>Yearly Summary</h3>';
      $this->printValueTable($this->year, $this->turnover);

      foreach ($this->quarters as $number => $types) {
        echo '<h3>'.$number.'. Quartal</h3>';
        $this->printValueTable($types, $this->quarter_turnovers[$number]);
      }

      echo '<h3 class="distribution_chart">Income Distribution</h3>';
      echo '<p>The following chart illustrates the distribution of the income for given year across the different income sources.';
      echo '<div id="income_chart" class="distribution_chart">';
      echo '</div>';

      echo '<h3 class="distribution_chart">Expense Distribution</h3>';
      echo '<p>The following chart illustrates the distribution of the expenses for given year across the different vendors.';
      echo '<div id="expense_chart" class="distribution_chart">';
      echo '</div>';
    }
  }

?>
