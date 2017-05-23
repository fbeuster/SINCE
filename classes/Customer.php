<?php

  class Customer {

    private $color;
    private $id;
    private $is_loaded = false;
    private $name;

    public function __construct($id) {
      $this->id = $id;
      $this->load();

      if (!$this->is_loaded) {
        return null;
      }
    }

    /**
     * getter for color
     * @return String
     */
    public function getColor() { return $this->color; }

    /**
     * getter for id
     * @return int
     */
    public function getId() { return $this->id; }

    /**
     * getter for name
     * @return String
     */
    public function getName() { return $this->name; }


    private function load() {
      $db = Database::getDB();

      $fields = array('name', 'color');
      $conds  = array('id = ?', 'i', array($this->id));
      $res    = $db->select('customers', $fields, $conds);

      if (count($res)) {
        $this->color      = $res[0]['color'];
        $this->name       = $res[0]['name'];
        $this->is_loaded  = true;
      }
    }

    /**
     * setter for color
     * @param String $color to set
     */
    public function setColor($color) { $this->color = $color; }

    /**
     * setter for id
     * @param int $id to set
     */
    public function setId($id) { $this->id = $id; }

    /**
     * setter for name
     * @param String $name to set
     */
    public function setName($name) { $this->name = $name; }
  }
?>
