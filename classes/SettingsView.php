<?php

  class SettingsView implements IView {

    private $categories = array();
    private $customers = array();
    private $scripts = '';

    public function __construct() {
      $this->handlePost();
      $this->loadData();
    }

    public function getScripts() {
      return $this->scripts;
    }

    public function getTitle() {
      return 'Settings';
    }

    private function handlePost() {
      if (isset($_POST)) {
        if (isset($_POST['save_currency'])) {
          if (preg_match('#(eur|usd)#', $_POST['currency'])) {
            Settings::set('currency', $_POST['currency']);

          } else {
            # TODO some currency save error
          }

        } else if (isset($_POST['save_language'])) {
          # TODO this check should look at available languages
          if (preg_match('#(de|en)#', $_POST['language'])) {
            Settings::set('language', $_POST['language']);

          } else {
            # TODO some language save error
          }
        }
      }
    }

    private function loadData() {
      $db     = Database::getDB();
      $fields = array('id');

      $customers = $db->select('customers', $fields);

      if (count($customers)) {
        foreach ($customers as $customer) {
          $customer = new Customer($customer['id']);

          if ($customer != null) {
            $this->customers[] = $customer;
          }
        }
      }

      $categories = $db->select('categories', $fields);

      if (count($categories)) {
        foreach ($categories as $category) {
          $category = new Category($category['id']);

          if ($category != null) {
            $this->categories[] = $category;
          }
        }
      }

      $this->currency = Settings::get('currency');
      $this->language = Settings::get('language');

      $this->currencies = array(
                            'eur' => 'Euro €',
                            'usd' => 'US-Dollar $');
      $this->languages  = array(
                            'de'  => 'Deutsch',
                            'en'  => 'English');
    }

    public function show() {
      include 'views/settings.php';
    }
  }

?>
