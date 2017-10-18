<?php
include 'includes/includes.php';
header('Content-Type: application/json');

try {
  $link = new DB;
  $cliente = new Contacto($link);

  $editar = false;
  $busqueda = false;

  if(isset($_POST['id'])) {
    $id = $_POST['id'];
    $cliente->buscar($id);

    $busqueda = true;
  }

  if(isset($_POST['clienteID'])) {
    $cliente->setCliente($_POST['clienteID']);
    $editar = true;
  }
  if(isset($_POST['nombre'])) {
    $cliente->setNombre($_POST['nombre']);
    $editar = true;
  }
  if(isset($_POST['mail'])) {
    $cliente->setMail($_POST['mail']);
    $editar = true;
  }
  if(isset($_POST['celular'])) {
    $cliente->setCelular($_POST['celular']);
    $editar = true;
  }
  if(isset($_POST['fijo'])) {
    $cliente->setFijo($_POST['fijo']);
    $editar = true;
  }
  if(isset($_POST['interno'])) {
    $cliente->setInterno($_POST['interno']);
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
