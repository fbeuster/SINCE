<?php

  class TransactionForm {
    private $categories = array();
    private $has_data   = false;
    private $is_valid   = false;
    private $success    = false;

    public function __construct() {
      $this->handlePost();
      $this->loadData();
    }

    private function handlePost() {
      if (isset($_POST, $_POST['add_transaction'])) {
        $this->has_data = true;
        $this->validate();
      }

      if ($this->is_valid) {
        $db     = Database::getDB();

        # is existing customer?
        $fields = array('id');
        $conds  = array('name = ?', 's', array($_POST['customer']));
        $res    = $db->select('customers', $fields, $conds);

        if (count($res) > 0) {
          $id = $res[0]['id'];

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
      if (!empty($this->errors)) {
        echo '<div class="form errors">';
        echo '<div class="title">Error</div>';
        echo '<ul>';
        foreach ($this->errors as $key => $error) {
          echo '<li>'.$error.'</li>';
        }
        echo '</ul>';
        echo '</div>';
      }

      if ($this->has_data && $this->success) {
        echo '<div class="form success">';
        echo '<div class="title">Success</div>';
        echo '<ul>';
        echo '<li>The transaction has been added.</li>';
        echo '</ul>';
        echo '</div>';
      }

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

    private function validate() {
      $brutto = 0;
      $netto  = 0;
      $tax7   = 0;
      $tax19  = 0;

      if (!isset($_POST['date']) || trim($_POST['date']) == '') {
        $this->errors['date'] = 'empty date';

      } else if (!strtotime($_POST['date'])) {
        $this->errors['date'] = 'invalid date';
      }

      if (!isset($_POST['customer']) || trim($_POST['customer']) == '') {
        $this->errors['customer'] = 'empty customer';
      }

      if (!isset($_POST['description']) || trim($_POST['description']) == '') {
        $this->errors['description'] = 'empty description';
      }

      if (!isset($_POST['category']) || trim($_POST['category']) == '') {
        $this->errors['category'] = 'empty category';

      } else if (trim($_POST['category']) == '-1') {
        $this->errors['category'] = 'no category';
      }

      if (!isset($_POST['netto']) || trim($_POST['netto']) == '') {
        $this->errors['netto'] = 'empty netto';

      } else if (!is_numeric(trim($_POST['netto']))) {
        $this->errors['netto'] = 'netto nan';

      } else if (trim($_POST['netto']) == 0) {
        $this->errors['netto'] = 'netto can\'t be zero';

      } else {
        $netto = trim($_POST['netto']);
      }

      if (!isset($_POST['brutto']) || trim($_POST['brutto']) == '') {
        $this->errors['brutto'] = 'empty brutto';

      } else if (!is_numeric(trim($_POST['brutto']))) {
        $this->errors['brutto'] = 'brutto nan';

      } else if (trim($_POST['brutto']) == 0) {
        $this->errors['brutto'] = 'brutto can\'t be zero';

      } else {
        $brutto = trim($_POST['brutto']);
      }

      if (!isset($_POST['tax7']) || trim($_POST['tax7']) == '') {
        $tax7 = 0;

      } else if (!is_numeric(trim($_POST['tax7']))) {
        $this->errors['tax7'] = 'tax7 nan';

      } else {
        $tax7 = trim($_POST['tax7']);
      }

      if (!isset($_POST['tax19']) || trim($_POST['tax19']) == '') {
        $tax19 = 0;

      } else if (!is_numeric(trim($_POST['tax19']))) {
        $this->errors['tax19'] = 'tax19 nan';

      } else {
        $tax19 = trim($_POST['tax19']);
      }

      if (empty($errors)) {
        $this->is_valid = true;
      }

      # make warnings for
      #   both taxes not 0
      #   values not adding up
    }
  }

?>
