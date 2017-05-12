<?php

  if (isset($_POST['search_term'], $_POST['field'])) {
    $db = Database::getDB();

    if ($_POST['field'] == 'customer') {
      $field  = 'name';
      $table  = 'customers';

    } else {
      $field  = $_POST['field'];
      $table  = 'transactions';
    }

    $fields   = array($field);
    $conds    = array($field.' LIKE ?', 's',
                      array('%'.$_POST['search_term'].'%'));
    $options  = 'GROUP BY '.$field.
                ' ORDER BY '.$field.' ASC';
    $limit    = array('LIMIT ?', 'i', array(5));
    $res      = $db->select($table, $fields, $conds,
                                    $options, $limit);

    if (count($res) > 0) {
      $customers = array();

      foreach ($res as $row) {
        $customers[] = $row[$field];
      }

      echo json_encode($customers);
    }
  }

?>
