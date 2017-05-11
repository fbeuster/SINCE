<?php

  function __autoload($class) {
    include_once 'classes/'.$class.'.php';
  }

  include 'local.php';

  if (!isset($_GET['view'])) {
    $view = '';

  } else {
    $view = trim($_GET['view']);
  }

  switch ($view) {
    case 'history': $view = new HistoryView(); break;
    case 'summary': $view = new SummaryView(); break;
    default:        $view = new HistoryView(); break;
  }

  $form = new TransactionForm();
?>

<!DOCTYPE html>
<html dir="ltr" lang="de-DE">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Incomes und Expenses</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <script type="text/javascript">
      var app = {};
    </script>
  </head>
  <body>
    <header>
      <div class="wrapper">
        <h1>Business Incomes und Expenses</h1>
        <nav>
          <menu>
            <li><a href="/history">Transaction History</a></li>
            <li><a href="/summary">Summaries</a></li>
          </menu>
        </nav>
      </div>
    </header>
    <div class="wrapper">
      <?php $form->show(); ?>

      <h2><?php echo $view->getTitle(); ?></h2>
      <?php $view->show(); ?>
    </div>
  </body>
  <script src="//d3js.org/d3.v3.min.js"></script>
  <?php echo $view->getScripts(); ?>
</html>