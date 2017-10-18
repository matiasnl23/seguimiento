<?php
include 'includes/includes.php';
header('Content-Type: application/json');

try {
  $db = new DB;
  $pedido = new Pedido($db);

  $editar = false;
  $busqueda = false;

  if(isset($_POST['id'])) {
    $id = $_POST['id'];
    $pedido->buscar($id);

    $busqueda = true;
  }

  if(isset($_POST['usuarioID']))
    $pedido->setUsuario($_POST['usuarioID']);

  if(isset($_POST['clienteID'])) {
    $pedido->setCliente($_POST['clienteID']);
    $editar = true;
  }
  if(isset($_POST['contactoID'])) {
    $pedido->setContacto($_POST['contactoID']);
    $editar = true;
  }
  if(isset($_POST['categoriaID'])) {
    $pedido->setCategoria($_POST['categoriaID']);
    $editar = true;
  }

  if(isset($_POST['titulo'])) {
    $pedido->setTitulo($_POST['titulo']);
    $editar = true;
  }
  if(isset($_POST['descripcion'])) {
    $pedido->setDescripcion($_POST['descripcion']);
    $editar = true;
  }

  if(isset($_POST['responsableID']))
    $pedido->setResponsable($_POST['responsableID']);

  if(isset($_POST['estado'])) {
    switch ($_POST['estado']) {
      case 1:
        $pedido->activar();
        break;
      case 2:
        $pedido->cancelar();
        break;
      case 7:
        $pedido->posponer();
        break;

      default:
        throw new JsonException("El estado seleccionado para este pedido no es válido.", 401);
        break;
    }
  }

  if($editar) {
    $pedido->guardar();
  }

  if($busqueda || $editar) {
    echo json_encode($pedido->detalles());
  } else {
    echo json_encode($pedido->buscarTodos());
  }


} catch (JsonException $e) {
  if($db->inTransaction())
    $db->rollBack();
  $e->getJson();
}



?>
