<?php
define('DB_NAME', 'seguimiento');
define('DB_HOSTNAME', 'localhost');
define('DB_USER', 'root');
define('DB_PASSWORD', '');


spl_autoload_register(function ($clase) {
  $clase = strtolower($clase);

  if(file_exists('includes/clases/class_'.$clase.'.php')) {
    include('includes/clases/class_'.$clase.'.php');
  }
});

define('REGEX_NOMBRE', array(
  '/^[a-záéíóúÁÉÍÓÚ]+\s[a-záéíóúÁÉÍÓÚ]+$/i',
  '/^[a-záéíóúÁÉÍÓÚ]+\s[a-záéíóúÁÉÍÓÚ]+\s[a-záéíóúÁÉÍÓÚ]+$/i',
  '/^[a-záéíóúÁÉÍÓÚ]+\s[a-záéíóúÁÉÍÓÚ]+\s[a-záéíóúÁÉÍÓÚ]+\s[a-záéíóúÁÉÍÓÚ]+$/i'
));

define('REGEX_CATEGORIA', '/^[\w\sáéíóúÁÉÍÓÚ]+$/i');

define('COTIZACION_PATH', 'cotizaciones/');
define('OC_PATH', 'oc/');
?>
