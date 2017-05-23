<?php

  class SummaryView implements IView {
    private $quarter_turnovers;
    private $quarters;
    private $scripts = '';
    private $turnover;
    private $year;

    public function __construct() {
      $this->loadData();
    }

    public function getScripts() {
      $this->scripts .= $this->income_chart->getJavaScripts();
      $this->scripts .= $this->expense_chart->getJavaScripts();
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

      $this->tables   = array();
      $this->tables[] = array(
                    'title'     => 'Yearly Summary',
                    'turnover'  => $this->turnover,
                    'types'     => $this->year);

      foreach ($this->quarters as $number => $types) {
        $this->tables[] = array(
                      'title'     => $number . '. Quarter',
                      'turnover'  => $this->quarter_turnovers[$number],
                      'types'     => $types);
      }

      $this->income_chart   = new IncomeChart();
      $this->expense_chart  = new ExpenseChart();
    }

    public function show() {
      include 'views/summary.php';
    }
  }

?>
