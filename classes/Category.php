<?php

  class Category {

    private $id;
    private $is_income;
    private $is_loaded;
    private $name;
    private $type;

    public function __construct($id) {
      $this->id = $id;
      $this->load();

      if (!$this->is_loaded) {
        return null;
      }
    }

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

    /**
     * getter for type
     * @return String
     */
    public function getType() { return $this->type; }

    /**
     * is income
     * @return bool
     */
    public function isIncome() { return $this->is_income; }

    private function load() {
      $db = Database::getDB();

      $fields = array('name', 'is_income', 'type');
      $conds  = array('id = ?', 'i', array($this->id));
      $res    = $db->select('categories', $fields, $conds);

      if (count($res)) {
        $this->is_income  = $res[0]['is_income'];
        $this->name       = $res[0]['name'];
        $this->type       = $res[0]['type'];
        $this->is_loaded  = true;
      }
    }

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

    /**
     * setter for type
     * @param String $type to set
     */
    public function setType($type) { $this->type = $type; }
  }

?>
