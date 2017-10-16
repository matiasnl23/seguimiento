<?php
include 'includes.php';
header('Content-Type: application/json');

try {
  $link = new DB;
  $cliente = new Cliente($link);

  if(isset($_POST['id'])) {

    $id = $_POST['id'];

    $cliente->buscar($id);
  }

  if(isset($_POST['nombre']))
    $cliente->setNombre($_POST['nombre']);
  if(isset($_POST['domicilio']))
    $cliente->setDomicilio($_POST['domicilio']);
  if(isset($_POST['aliases']))
    $cliente->setAliases($_POST['aliases']);

  if(isset($_POST['nombre']) || isset($_POST['domicilio']) || isset($_POST['aliases'])) {

    $cliente->guardar();
  }

  echo json_encode($cliente->detalles());


} catch (JsonException $e) {
  $e->getJson();
}



?>
