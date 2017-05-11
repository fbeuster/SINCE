<?php

  class TransactionForm {
    private $categories = array();
    private $success    = false;

    public function __construct() {
      $this->handlePost();
      $this->loadData();
    }

    private function handlePost() {
      if (isset($_POST, $_POST['add_transaction'])) {
        $db     = Database::getDB();
        $fields = array('date', 'customer', 'description', 'category_id',
                        'netto', 'tax_7', 'tax_19', 'brutto');
        $values = array('sssidddd',
                        array($_POST['date'], $_POST['customer'],
                              $_POST['description'], $_POST['category'],
                              $_POST['netto'], $_POST['tax_7'],
                              $_POST['tax_19'], $_POST['brutto']
        ));
        $id = $db->insert('transactions', $fields, $values);

        if ($id) {
          $this->success = true;
        }
      }
    }

    private function loadData() {
      $db     = Database::getDB();
      $fields = array('id', 'name');
      $res    = $db->select('categories', $fields);

      if (count($res)) {
        foreach ($res as $row) {
          $this->categories[ $row['id'] ] = $row['name'];
        }
      }
    }

    private function makeInputDate($name, $value) {
      $date = '';
      $date .= '<td>';
      $date .= '<input type="date" name="'.$name.'" placeholder="YYYY-MM-DD" value="'.$value.'">';
      $date .= '</td>';

      return $date;
    }

    private function makeInputNumber($name, $value) {
      $number = '';
      $number .= '<td>';
      $number .= '<input type="number" step="0.01" value="'.$value.'" name="'.$name.'">';
      $number .= '</td>';

      return $number;
    }

    private function makeInputText($name, $value, $length, $placeholder) {
      $text = '';
      $text .= '<td>';
      $text .= '<input type="text" name="'.$name.'" maxlength="'.$length.'" placeholder="'.$placeholder.'" value='.$value.'>';
      $text .= '</td>';

      return $text;
    }

    private function makeSelect($name, $value, $options) {
      $options = array('-1' => 'Select category...') + $options;

      $select = '';
      $select .= '<td>';
      $select .= '<select name="'.$name.'">';

      foreach ($options as $key => $label) {
        $select .= '<option value="'.$key.'"';

        if (trim($value) == $key) {
          $select .= ' selected="selected"';
        }

        $select .= '>';
        $select .= $label;
        $select .= '</option>';
      }

      $select .= '</select>';
      $select .= '</td>';

      return $select;
    }

    public function show() {
      echo '<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
      echo '<fieldset>';
      echo '<legend>Add Transaction</legend>';

      echo '<table>';
      echo '<thead>';
      echo '<tr>';
      echo '<td>Date</td>';
      echo '<td>Customer</td>';
      echo '<td>Description</td>';
      echo '<td>Netto</td>';
      echo '<td>7%</td>';
      echo '<td>19%</td>';
      echo '<td>Brutto</td>';
      echo '<td>Category</td>';
      echo '</tr>';
      echo '</thead>';

      echo '<tbody>';
      echo '<tr>';
      echo $this->makeInputDate('date', '');
      echo $this->makeInputText('customer', '', 64,
                                'Customer name');
      echo $this->makeInputText('description', '', 128,
                                'Description text');
      echo $this->makeInputNumber('netto', 0);
      echo $this->makeInputNumber('tax_7', 0);
      echo $this->makeInputNumber('tax_19', 0);
      echo $this->makeInputNumber('brutto', 0);
      echo $this->makeSelect('category', '', $this->categories);
      echo '</tr>';
      echo '<tr>';
      echo '<td colspan="7"></td>';
      echo '<td class="submit">';
      echo '<input type="submit" value="Insert" name="add_transaction">';
      echo '</td>';
      echo '</tr>';
      echo '</tbody>';
      echo '</table>';
      echo '</fieldset>';
      echo '</form>';
    }
  }

?>
