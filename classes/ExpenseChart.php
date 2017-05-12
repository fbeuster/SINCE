<?php

  class ExpenseChart {
    private $expenses = array();
    private $total = 0;

    function __construct() {
      $this->loadData();
    }

    public function getJavaScripts() {
      $scripts = '';

      $scripts .= '<script type="text/javascript" src="assets/js/distribution_chart.js"></script>';
      $scripts .= '<script type="text/javascript">';
      $scripts .= 'var expense_chart = $.extend(true, {}, app.distribution_chart);';
      $scripts .= "expense_chart.draw('#expense_chart', JSON.parse('".json_encode($this->expenses)."'));";
      $scripts .= '</script>';

      return $scripts;
    }

    private function loadData() {
      $con = Database::getDB()->getCon();
      $sql = 'SELECT
                `customers`.`name`,
                `customers`.`color`,
                ROUND(SUM(`brutto`), 2) AS `brutto_sum`
              FROM
                `transactions`
              JOIN
                `categories`
                ON `categories`.`id` = `transactions`.`category_id`
              JOIN
                `customers`
                ON `customers`.`id` = `transactions`.`customers_id`
              WHERE
                `is_income` = 0
              GROUP BY
                `customers`.`name`
              ORDER BY
                `brutto_sum` DESC';

      $stmt   = $con->prepare($sql);
      $stmt->execute();
      $stmt->bind_result($customer, $color, $brutto);

      while ($stmt->fetch()) {
        $this->total += $brutto;
        $this->expenses[] = array(
          'c' => $customer,
          'color' => ($color && preg_match('/#[a-f0-9]{6}/', $color)) ? $color : '',
          'amount' => $brutto
        );
      }
      $stmt->close();
    }

    public function show() {
      echo '<h3 class="distribution_chart">Expense Distribution</h3>';
      echo '<p>The following chart illustrates the distribution of the expenses for given year across the different vendors.';
      echo '<div id="expense_chart" class="distribution_chart" data-sum="'.$this->total.'">';
      echo '</div>';
    }
  }
?>
