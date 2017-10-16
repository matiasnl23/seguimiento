<?php
/**
 *  Clase de manejo de la base de datos
 */

class DB extends PDO {

  function __construct() {
    $db = DB_NAME;
    $host = DB_HOSTNAME;
    $username = DB_USER;
    $password = DB_PASSWORD;

    parent::__construct("mysql:dbname=$db;host=$host;charset=UTF8", $username, $password);
  }
}
?>
