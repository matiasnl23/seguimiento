<?php
include 'includes.php';
header('Content-Type: application/json');

try {
  $link = new DB;
  $cliente = new Contacto($link);

  if(isset($_POST['id'])) {

    $id = $_POST['id'];

    $cliente->buscar($id);
  }

  if(isset($_POST['clienteID']))
    $cliente->setCliente($_POST['clienteID']);
  if(isset($_POST['nombre']))
    $cliente->setNombre($_POST['nombre']);
  if(isset($_POST['mail']))
    $cliente->setMail($_POST['mail']);
  if(isset($_POST['celular']))
    $cliente->setCelular($_POST['celular']);
  if(isset($_POST['fijo']))
    $cliente->setFijo($_POST['fijo']);
  if(isset($_POST['interno']))
    $cliente->setInterno($_POST['interno']);

  if(isset($_POST['clienteID']) || isset($_POST['nombre']) || isset($_POST['mail']) || isset($_POST['celular']) || isset($_POST['fijo']) || isset($_POST['interno'])) {

    $cliente->guardar();
  }

  echo json_encode($cliente->detalles());



} catch (JsonException $e) {
  $e->getJson();
}



?>
