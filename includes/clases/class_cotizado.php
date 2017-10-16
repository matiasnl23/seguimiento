<?php

class Cotizado extends Pedido {

  public function guardar() {
    $this->db->beginTransaction();

    if(!$this->pedido['pedidoID'])
      throw new JsonException("No se ha declarado el pedido a cuál asignar el estado.", 400);


    $q = $this->db->prepare('UPDATE tblPedidos SET clienteID = :clienteID, contactoID = :contactoID, categoriaID = :categoriaID, titulo = :titulo, descripcion = :descripcion WHERE pedidoID = :pedidoID');

    $q->bindParam(':pedidoID', $this->pedido['pedidoID'], PDO::PARAM_INT);
    $q->bindParam(':clienteID', $this->pedido['clienteID'], PDO::PARAM_INT);
    $q->bindParam(':contactoID', $this->pedido['contactoID'], PDO::PARAM_INT);
    $q->bindParam(':categoriaID', $this->pedido['categoriaID'], PDO::PARAM_INT);

    $q->bindParam(':titulo', $this->pedido['titulo'], PDO::PARAM_STR);
    $q->bindParam(':descripcion', $this->pedido['descripcion'], PDO::PARAM_STR);

    $this->validar();

    if(!$q->execute())
      throw new JsonException($q->errorInfo(), 500, true);

    if(!$this->pedido['pedidoID'])
      $this->pedido['pedidoID'] = $this->db->lastInsertId();

    $this->db->commit();
  }

  public function vigente() {
    if(is_null($this->pedido['pedidoID']))
      throw new JsonException("No se ha declarado el pedido a cuál asignar el estado.", 400);

    $this->pedido['estado'] = 4;

    $q = $this->db->prepare('UPDATE tblPedidos SET estado = :estado WHERE pedidoID = :pedidoID');
    $q->bindParam(':estado', $this->pedido['estado'], PDO::PARAM_INT);
    $q->bindParam(':pedidoID', $this->pedido['pedidoID'], PDO::PARAM_INT);

    if(!$q->execute())
      throw new JsonException($q->errorInfo(), 500, true);
  }
  public function no_vigente() {
    if(is_null($this->pedido['pedidoID']))
      throw new JsonException("No se ha declarado el pedido a cuál asignar el estado.", 400);

    $this->pedido['estado'] = 5;

    $q = $this->db->prepare('UPDATE tblPedidos SET estado = :estado WHERE pedidoID = :pedidoID');
    $q->bindParam(':estado', $this->pedido['estado'], PDO::PARAM_INT);
    $q->bindParam(':pedidoID', $this->pedido['pedidoID'], PDO::PARAM_INT);

    if(!$q->execute())
      throw new JsonException($q->errorInfo(), 500, true);
  }
  public function rechazado() {
    if(is_null($this->pedido['pedidoID']))
      throw new JsonException("No se ha declarado el pedido a cuál asignar el estado.", 400);

    $this->pedido['estado'] = 3;

    $q = $this->db->prepare('UPDATE tblPedidos SET estado = :estado WHERE pedidoID = :pedidoID');
    $q->bindParam(':estado', $this->pedido['estado'], PDO::PARAM_INT);
    $q->bindParam(':pedidoID', $this->pedido['pedidoID'], PDO::PARAM_INT);

    if(!$q->execute())
      throw new JsonException($q->errorInfo(), 500, true);
  }

  public function subir_oc() {

  }
}

?>
