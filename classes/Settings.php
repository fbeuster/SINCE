<?php

  class Settings {
    public static function get($key) {
      if (!$key)  {
        return null;
      }

      $db = Database::getDB();

      $fields = array('setting_value');
      $conds  = array('setting_key = ?', 's', array($key));
      $value  = $db->select('settings', $fields, $conds);

      if (count($value)) {
        return $value[0]['setting_value'];
      }

      return null;
    }

    public static function set($key, $value) {
      if (!$key || !$value) {
        return false;
      }

      $db_con = Database::getDB()->getCon();

      $sql = 'UPDATE settings SET setting_value = ? WHERE setting_key = ?';

      if (!$stmt = $db_con->prepare($sql)) {
        return false;
      }

      $stmt->bind_param('ss', $value, $key);

      if (!$stmt->execute()) {
        return false;
      }

      $stmt->close();

      return true;
    }
  }
?>
