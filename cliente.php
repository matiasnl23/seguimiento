<?php
include 'includes/includes.php';
header('Content-Type: application/json');

try {
  $link = new DB;
  $cliente = new Cliente($link);

  $editar = false;
  $busqueda = false;

  if(isset($_POST['id'])) {
    $id = $_POST['id'];
    $cliente->buscar($id);

    $busqueda = true;
  }

  if(isset($_POST['nombre'])) {
    $cliente->setNombre($_POST['nombre']);
    $editar = true;
  }
  if(isset($_POST['domicilio'])) {
    $cliente->setDomicilio($_POST['domicilio']);
    $editar = true;
  }
  if(isset($_POST['aliases'])) {
    $cliente->setAliases($_POST['aliases']);
    $editar = true;
  }

  if($editar) {
    $cliente->guardar();
  }

  if($busqueda || $editar) {
    echo json_encode($cliente->detalles());
  } else {
    echo json_encode($cliente->buscarTodos());
  }
} catch (JsonException $e) {
  $e->getJson();
}



?>
