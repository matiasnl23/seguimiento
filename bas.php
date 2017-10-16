<?php
include 'includes/includes.php';
header('Content-Type: application/json');

try {
  $link = new DB;
  $bas = new BAS($link);

  if(isset($_POST['area'])) {

    $area = $_POST['area'];

    $bas->buscar($area);
  }

  echo json_encode($bas->detalles());


} catch (JsonException $e) {
  $e->getJson();
}



?>
