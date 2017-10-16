<?php
define('DB_NAME', 'seguimiento');
define('DB_HOSTNAME', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');


spl_autoload_register(function ($clase) {
  $clase = strtolower($clase);

  if(file_exists('clases/class_'.$clase.'.php')) {
    include('clases/class_'.$clase.'.php');
  }
  if(file_exists('excepciones/class_'.$clase.'.php')) {
    include('excepciones/class_'.$clase.'.php');
  }

});

define('REGEX_NOMBRE', array(
  '/^[a-záéíóúÁÉÍÓÚ]+\s[a-záéíóúÁÉÍÓÚ]+$/i',
  '/^[a-záéíóúÁÉÍÓÚ]+\s[a-záéíóúÁÉÍÓÚ]+\s[a-záéíóúÁÉÍÓÚ]+$/i',
  '/^[a-záéíóúÁÉÍÓÚ]+\s[a-záéíóúÁÉÍÓÚ]+\s[a-záéíóúÁÉÍÓÚ]+\s[a-záéíóúÁÉÍÓÚ]+$/i'
))
?>
