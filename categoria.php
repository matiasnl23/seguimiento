<?php
include 'includes/includes.php';
header('Content-Type: application/json');

try {
  $link = new DB;
  $categoria = new Categoria($link);

  $editar = false;
  $busqueda = false;

  if(isset($_POST['id'])) {
    $id = $_POST['id'];
    $categoria->buscar($id);

    $busqueda = true;
  }

  if(isset($_POST['nombre'])) {
    $categoria->setNombre($_POST['nombre']);
    $editar = true;
  }

  if($editar) {
    $categoria->guardar();
  }

  if($busqueda || $editar) {
    echo json_encode($categoria->detalles());
  } else {
    echo json_encode($categoria->buscarTodos());
  }
} catch (JsonException $e) {
  $e->getJson();
}



?>
