<?php
include 'includes.php';
header('Content-Type: application/json');

try {
  $db = new DB;
  $pedido = new Pedido($db);

  if(isset($_POST['id'])) {

    $id = $_POST['id'];

    $pedido->buscar($id);
  }

  if(isset($_POST['usuarioID']))
    $pedido->setUsuario($_POST['usuarioID']);

  if(isset($_POST['clienteID']))
    $pedido->setCliente($_POST['clienteID']);
  if(isset($_POST['contactoID']))
    $pedido->setContacto($_POST['contactoID']);
  if(isset($_POST['categoriaID']))
    $pedido->setCategoria($_POST['categoriaID']);

  if(isset($_POST['titulo']))
    $pedido->setTitulo($_POST['titulo']);
  if(isset($_POST['descripcion']))
    $pedido->setDescripcion($_POST['descripcion']);

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

  if(isset($_POST['clienteID']) || isset($_POST['clienteID']) || isset($_POST['contactoID']) || isset($_POST['categoriaID']) || isset($_POST['titulo']) || isset($_POST['descripcion'])) {
    $pedido->guardar();
  }

  echo json_encode($pedido->detalles());


} catch (JsonException $e) {
  if($db->inTransaction())
    $db->rollBack();
  $e->getJson();
}



?>
