<?php

class FileLoader {

  public static function loadIni($path) {
    return parse_ini_file($path);
  }

  public static function loadJson($path, $as_array) {
    $file_content = FileLoader::file_get_contents_utf8($path);
    return json_decode($file_content, $as_array);
  }

  public static function file_get_contents_utf8($path) {
    $file_content = file_get_contents($path);

    if (!$file_content) {
      throw new Exception($path . ' not found.', 1);
    }

    if (substr($file_content, 0, 3) == pack("CCC", 0xEF, 0xBB, 0xBF)) {
      $file_content = substr($file_content, 3);
    }

    if ( !mb_check_encoding($file_content, 'UTF-8') ) {
      $file_content = mb_convert_encoding($file_content, 'UTF-8');
    }

    return $file_content;
  }
}

?>