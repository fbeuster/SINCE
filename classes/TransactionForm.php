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

        # is existing customer?
        $fields = array('id');
        $conds  = array('name = ?', 's', array($_POST['customer']));
        $res    = $db->select('customers', $fields, $conds);

        if (count($res) > 0) {
          $id = $res[0];

        } else {
          # no? then create new customer
          $fields = array('name', 'color');
          $values = array('ss', array($_POST['customer'],
                                      $_POST['color']));
          $id     = $db->insert('customers', $fields, $values);
        }

        # insert transaction
        $fields = array('date', 'description',
                        'category_id', 'customers_id',
                        'netto', 'tax_7', 'tax_19', 'brutto');
        $values = array('ssiidddd',
                        array($_POST['date'], $_POST['description'],
                              $_POST['category'], $id,
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

    private function makeInputColor($name, $value, $length, $placeholder, $label) {
      $text = '';
      $text .= '<label>';
      $text .= '<span>'.$label.'</span>';
      $text .= '<input type="color" name="'.$name.'" maxlength="'.$length.'" placeholder="'.$placeholder.'" title="Click to select a color" value="'.$value.'">';
      $text .= '</label>';

      return $text;
    }

    private function makeInputDate($name, $value, $label) {
      $date = '';
      $date .= '<label>';
      $date .= '<span>'.$label.'</span>';
      $date .= '<input type="date" name="'.$name.'" placeholder="YYYY-MM-DD" value="'.$value.'">';
      $date .= '</label>';

      return $date;
    }

    private function makeInputNumber($name, $value, $label) {
      $number = '';
      $number .= '<label class="number">';
      $number .= '<span>'.$label.'</span>';
      $number .= '<input type="number" step="0.01" value="'.$value.'" name="'.$name.'">';
      $number .= '</label>';

      return $number;
    }

    private function makeInputText($name, $value, $length, $placeholder, $label) {
      $text = '';
      $text .= '<label>';
      $text .= '<span>'.$label.'</span>';
      $text .= '<input type="text" name="'.$name.'" maxlength="'.$length.'" placeholder="'.$placeholder.'" value="'.$value.'" autocomplete="off" class="combo">';
      $text .= '</label>';

      return $text;
    }

    private function makeSelect($name, $value, $options, $label) {
      $options = array('-1' => 'Select category...') + $options;

      $select = '';
      $select .= '<label>';
      $select .= '<span>'.$label.'</span>';
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
      $select .= '</label>';

      return $select;
    }

    public function show() {
      echo '<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
      echo '<fieldset>';
      echo '<legend>Add Transaction</legend>';

      echo $this->makeInputDate('date', '', 'Date');
      echo $this->makeInputText('customer', '', 128,
                                'Customer name',
                                'Customer');
      echo $this->makeInputColor('color', '#ffffff', 7,
                                'Customer color',
                                'Color');
      echo $this->makeInputText('description', '', 128,
                                'Description text',
                                'Description');
      echo $this->makeSelect('category', '', $this->categories, 'Category');
      echo $this->makeInputNumber('netto', 0, 'Netto');
      echo $this->makeInputNumber('tax_7', 0, '7% Tax');
      echo $this->makeInputNumber('tax_19', 0, '19% Tax');
      echo $this->makeInputNumber('brutto', 0, 'Brutto');
      echo '<input type="submit" value="Insert transaction" name="add_transaction">';
      echo '</fieldset>';
      echo '</form>';
    }
  }

?>
