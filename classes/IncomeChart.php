<?php

  class IncomeChart {
    private $incomes = array();

    function __construct() {
      $this->loadData();
    }

    public function getJavaScripts() {
      $scripts = '';

      $scripts .= '<script type="text/javascript" src="assets/js/distribution_chart.js"></script>';
      $scripts .= '<script type="text/javascript">';
      $scripts .= 'var income_chart = $.extend(true, {}, app.distribution_chart);';
      $scripts .= "income_chart.draw('#income_chart', JSON.parse('".json_encode($this->incomes)."'));";
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
                `is_income` = 1
              GROUP BY
                `customers`.`name`
              ORDER BY
                `brutto_sum` DESC';

      $stmt   = $con->prepare($sql);
      $stmt->execute();
      $stmt->bind_result($customer, $color, $brutto);

      while ($stmt->fetch()) {
        $this->incomes[] = array(
          'c' => $customer,
          'color' => ($color && preg_match('/#[a-f0-9]{6}/', $color)) ? $color : '',
          'amount' => $brutto
        );
      }
      $stmt->close();
    }
  }
?>
