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
      return 'Transaction History';
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
      echo '<table class="value_list" id="transaction_history">';
      echo '<thead>'."\n";
      echo '<tr>';
      echo '<td class="date">Date</td>';
      echo '<td class="text">Customer</td>';
      echo '<td class="text">Description</td>';
      echo '<td class="number">Netto</td>';
      echo '<td class="number">7%</td>';
      echo '<td class="number">19%</td>';
      echo '<td class="number">Brutto</td>';
      echo '<td class="text">Category</td>';
      echo '<td class="actions"></td>';
      echo '</tr>'."\n";
      echo '</thead>';
      echo '<tbody>'."\n";

      if (!count($this->transactions)) {
        echo '<tr>';
        echo '<td colspan="8">No transactions</td>';
        echo '</tr>'."\n";

      } else {
        foreach ($this->transactions as $transaction) {
          echo '<tr data-transaction-id="'.$transaction['id'].'">';
          echo '<td class="date">';
          echo date('d.m.Y', strtotime($transaction['date']));
          echo '</td>';
          echo '<td class="text customer">';
          echo $transaction['customer'];
          echo '</td>';
          echo '<td class="text description">';
          echo $transaction['description'];
          echo '</td>';
          echo '<td class="number">';
          echo sprintf('%01.2f €', $transaction['netto']);
          echo '</td>';
          echo '<td class="number">';
          echo sprintf('%01.2f €', $transaction['tax_7']);
          echo '</td>';
          echo '<td class="number">';
          echo sprintf('%01.2f €', $transaction['tax_19']);
          echo '</td>';
          echo '<td class="number">';
          echo sprintf('%01.2f €', $transaction['brutto']);
          echo '</td>';
          echo '<td class="text">';
          echo $transaction['category'];
          echo '</td>';
          echo '<td class="actions">';
          echo '<span class="button mode_edit material-icons" title="Edit transaction">mode_edit</span>';
          echo '<span class="button delete material-icons" title="Delete transaction">delete</span>';
          echo '<span class="button done material-icons" title="Save changes">done</span>';
          echo '<span class="button cancel material-icons" title="Cancel">cancel</span>';
          echo '</td>';
          echo '</tr>'."\n";
        }
      }

      echo '</tbody>';
      echo '</table>'."\n";

      $this->form->show();
    }
  }

?>
