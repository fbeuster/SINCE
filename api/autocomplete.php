<?php

  if (isset($_POST['search_term'], $_POST['field'])) {
    $db = Database::getDB();

    $fields   = array($_POST['field']);
    $conds    = array($_POST['field'].' LIKE ?', 's',
                      array('%'.$_POST['search_term'].'%'));
    $options  = 'GROUP BY '.$_POST['field'].
                ' ORDER BY '.$_POST['field'].' ASC';
    $limit    = array('LIMIT ?', 'i', array(2));
    $res      = $db->select('transactions', $fields, $conds,
                                            $options, $limit);

    if (count($res) > 0) {
      $customers = array();

      foreach ($res as $row) {
        $customers[] = $row[$_POST['field']];
      }

      echo json_encode($customers);
    }
  }

?>
