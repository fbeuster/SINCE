<?php

  class HistoryView implements IView {
    private $form;
    private $scripts = '';
    private $transactions = array();

    public function __construct() {
      $this->loadData();
    }

    public function getScripts() {
      return $this->scripts;
    }

    public function getTitle() {
      return I18n::t('history.label');
    }

    private function loadData() {
      # loading form before data to handle submits
      $this->form = new TransactionForm();

      $db       = Database::getDB();
      $fields   = array('transactions.id',
                        'transactions.date', 'customers.name AS cname',
                        'transactions.description', 'transactions.netto',
                        'transactions.tax_7', 'transactions.tax_19',
                        'transactions.brutto',
                        'categories.name');
      $options  = 'ORDER BY date ASC';
      $join     = 'JOIN categories ON categories.id = transactions.category_id '.
                  'JOIN customers ON customers.id = transactions.customers_id';
      $res      = $db->select('transactions', $fields,
                              null, $options, null, $join);

      if (count($res)) {
        foreach ($res as $row) {
          $this->transactions[] = array(
            'brutto'      => $row['brutto'],
            'category'    => $row['name'],
            'customer'    => $row['cname'],
            'date'        => $row['date'],
            'description' => $row['description'],
            'id'          => $row['id'],
            'netto'       => $row['netto'],
            'tax_7'       => $row['tax_7'],
            'tax_19'      => $row['tax_19']
          );
        }
      }

      $this->scripts .= '<script type="text/javascript" src="assets/js/transaction_history.js"></script>';
      $this->scripts .= '<script type="text/javascript">';
      $this->scripts .= "app.transaction_history.init('#transaction_history');";
      $this->scripts .= '</script>';
    }

    public function show() {
      include 'views/history.php';
    }
  }

?>
