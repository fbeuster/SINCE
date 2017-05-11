<?php

  function __autoload($class) {
    include_once 'classes/'.$class.'.php';
  }

  include 'local.php';

  if (isset($_POST['action'])) {
    if (file_exists('api/'.$_POST['action'].'.php')) {
      include 'api/'.$_POST['action'].'.php';
    }
  }

  echo '';

?>
