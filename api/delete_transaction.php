<?php

  if (isset($_POST['transaction_id'])) {
    $db = Database::getDB();

    $conds    = array('id = ?', 'i', array($_POST['transaction_id']));
    $deleted  = $db->delete('transactions', $conds);

    if (!$deleted) {
      echo 'error';
    } else {
      echo 'success';
    }
  }

?>
