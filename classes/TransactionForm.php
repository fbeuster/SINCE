<?php

  class TransactionForm {
    private $categories = array();
    private $has_data   = false;
    private $is_valid   = false;
    private $success    = false;

    public function __construct() {
      $this->loadDefaultValues();
      $this->handlePost();
      $this->loadData();
    }

    private function handlePost() {
      if (isset($_POST, $_POST['add_transaction'])) {
        $this->has_data = true;

        foreach ($this->values as $key => $value) {
          if (isset($_POST[$key])) {
            $this->values[$key] = trim($_POST[$key]);
          }
        }

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
          $fields = array('name');
          $values = array('s', array($_POST['customer']));

          if (isset($_POST['set_color']) &&
              $_POST['set_color']) {
            $fields[]     = 'color';
            $values[0]    .= 's';
            $values[1][]  = $_POST['color'];
          }

          $id = $db->insert('customers', $fields, $values);
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
          $this->loadDefaultValues();
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

    private function loadDefaultValues() {
      $this->values   = array(
        'date'        => '',
        'customer'    => '',
        'set_color'   => 0,
        'color'       => '#ffffff',
        'description' => '',
        'category'    => '',
        'netto'       => 0,
        'tax_7'       => 0,
        'tax_19'      => 0,
        'brutto'      => 0
      );
    }

    private function makeInputCheck($name, $label) {
      $check = '';
      $check .= '<label';

      if (isset($this->errors[$name])) {
        $check .= ' class="has_error"';
      }

      $check .= '>';
      $check .= '<span>'.$label.'</span>';
      $check .= '<input type="checkbox" name="'.$name.'" title="'.$label.'"';

      if ($this->values[$name]) {
        $check .= ' checked="checked"';
      }

      $check .= '>';
      $check .= '</label>'."\n";

      return $check;
    }

    private function makeInputColor($name, $length, $placeholder, $label) {
      $text = '';
      $text .= '<label class="color';

      if (isset($this->errors[$name])) {
        $text .= ' has_error';
      }

      $text .= '">';
      $text .= '<span>'.$label.'</span>';
      $text .= '<input type="color" name="'.$name.'" maxlength="'.$length.'" placeholder="'.$placeholder.'" title="Click to select a color" value="'.$this->values[$name].'">';
      $text .= '</label>'."\n";

      return $text;
    }

    private function makeInputDate($name, $label) {
      $date = '';
      $date .= '<label class="required';

      if (isset($this->errors[$name])) {
        $date .= ' has_error';
      }

      $date .= '">';
      $date .= '<span>'.$label.'</span>';
      $date .= '<input type="date" name="'.$name.'" placeholder="YYYY-MM-DD" value="'.$this->values[$name].'" required="required">';
      $date .= '</label>'."\n";

      return $date;
    }

    private function makeInputNumber($name, $label, $required = false) {
      $number = '';
      $number .= '<label class="number';

      if ($required) {
        $number .= ' required';
      }

      if (isset($this->errors[$name])) {
        $number .= ' has_error';
      }

      $number .= '">';
      $number .= '<span>'.$label.'</span>';
      $number .= '<input type="number" step="0.01" value="'.$this->values[$name].'" name="'.$name.'"';

      if ($required) {
        $number .= ' required="required"';
      }

      $number .= '>';
      $number .= '</label>';

      return $number;
    }

    private function makeInputText($name, $length, $placeholder, $label) {
      $text = '';
      $text .= '<label class="required';

      if (isset($this->errors[$name])) {
        $text .= ' has_error';
      }

      $text .= '">';
      $text .= '<span>'.$label.'</span>';
      $text .= '<input type="text" name="'.$name.'" maxlength="'.$length.'" placeholder="'.$placeholder.'" value="'.$this->values[$name].'" autocomplete="off" class="combo" required="required">';
      $text .= '</label>'."\n";

      return $text;
    }

    private function makeSelect($name, $options, $label) {
      $options = array('-1' => 'Select category...') + $options;

      $select = '';
      $select .= '<label class="required';

      if (isset($this->errors[$name])) {
        $select .= ' has_error';
      }

      $select .= '">';
      $select .= '<span>'.$label.'</span>';
      $select .= '<select name="'.$name.'" required="required">';

      foreach ($options as $key => $label) {
        if ('-1' == $key) {
          $key = '';
        }

        $select .= '<option value="'.$key.'"';

        if ($this->values[$name] == $key) {
          $select .= ' selected="selected"';
        }

        $select .= '>';
        $select .= $label;
        $select .= '</option>';
      }

      $select .= '</select>';
      $select .= '</label>'."\n";

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

      echo '<form action="'.$_SERVER['REQUEST_URI'].'" method="post">'."\n";
      echo '<fieldset>'."\n";
      echo '<legend>Add Transaction</legend>'."\n";

      echo $this->makeInputDate('date', 'Date');
      echo $this->makeInputText('customer', 128,
                                'Customer name',
                                'Customer');
      echo $this->makeInputCheck('set_color', 'Assign color');
      echo $this->makeInputColor('color', '#ffffff', 7,
                                'Customer color',
                                'Color');
      echo $this->makeInputText('description', 128,
                                'Description text',
                                'Description');
      echo $this->makeSelect('category', $this->categories, 'Category');
      echo $this->makeInputNumber('netto', 'Netto', true);
      echo $this->makeInputNumber('tax_7', '7% Tax');
      echo $this->makeInputNumber('tax_19', '19% Tax');
      echo $this->makeInputNumber('brutto', 'Brutto', true);
      echo '<p>Fields marked with a <span>*</span> are required and must be filled appropiately.</p>'."\n";
      echo '<input type="submit" value="Insert transaction" name="add_transaction">'."\n";
      echo '</fieldset>'."\n";
      echo '</form>'."\n";
    }

    private function validate() {
      $brutto = 0;
      $netto  = 0;
      $tax7   = 0;
      $tax19  = 0;

      if ($this->values['date'] == '') {
        $this->errors['date'] = 'empty date';

      } else if (!strtotime($this->values['date'])) {
        $this->errors['date'] = 'invalid date';
      }

      if ($this->values['customer'] == '') {
        $this->errors['customer'] = 'empty customer';
      }

      if ($this->values['set_color'] &&
          !preg_match('/#[0-9a-f]{6}/', $this->values['color'])) {
        $this->errors['color'] = 'invalid color';
      }

      if ($this->values['description'] == '') {
        $this->errors['description'] = 'empty description';
      }

      if ($this->values['category'] == '') {
        $this->errors['category'] = 'empty category';
      }

      if ($this->values['netto'] == '') {
        $this->errors['netto'] = 'empty netto';

      } else if (!is_numeric($this->values['netto'])) {
        $this->errors['netto'] = 'netto nan';

      } else if ($this->values['netto'] == 0) {
        $this->errors['netto'] = 'netto can\'t be zero';

      } else {
        $netto = $this->values['netto'];
      }

      if ($this->values['brutto'] == '') {
        $this->errors['brutto'] = 'empty brutto';

      } else if (!is_numeric($this->values['brutto'])) {
        $this->errors['brutto'] = 'brutto nan';

      } else if ($this->values['brutto'] == 0) {
        $this->errors['brutto'] = 'brutto can\'t be zero';

      } else {
        $brutto = $this->values['brutto'];
      }

      if ($this->values['tax_7'] == '') {
        $tax7 = 0;

      } else if (!is_numeric($this->values['tax_7'])) {
        $this->errors['tax_7'] = 'tax7 nan';

      } else {
        $tax7 = $this->values['tax_7'];
      }

      if ($this->values['tax_19'] == '') {
        $tax19 = 0;

      } else if (!is_numeric($this->values['tax_19'])) {
        $this->errors['tax_19'] = 'tax19 nan';

      } else {
        $tax19 = $this->values['tax_19'];
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
