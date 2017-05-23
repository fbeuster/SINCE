<?php

  class SettingsView implements IView {

    private $categories = array();
    private $customers = array();
    private $scripts = '';

    public function __construct() {
      $this->loadData();
    }

    public function getScripts() {
      return $this->scripts;
    }

    public function getTitle() {
      return 'Settings';
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
    }

    public function show() {
      include 'views/settings.php';
    }
  }

?>
