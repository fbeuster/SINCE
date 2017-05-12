<?php

  class IncomeChart {
    private $incomes = array();

    function __construct() {
      $this->loadData();
    }

    public function getJavaScripts() {
      $scripts = '';

      $scripts .= '<script type="text/javascript" src="assets/js/income_chart.js"></script>';
      $scripts .= '<script type="text/javascript">';
      $scripts .= "app.income_chart.draw(JSON.parse('".json_encode($this->incomes)."'));";
      $scripts .= '</script>';

      return $scripts;
    }

    private function loadData() {
      $con = Database::getDB()->getCon();
      $sql = 'SELECT
                `customers`.`name`,
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
      $stmt->bind_result($customer, $brutto);

      while ($stmt->fetch()) {
        $this->incomes[] = array(
          'c' => $customer,
          'amount' => $brutto
        );
      }
      $stmt->close();
    }
  }
?>
