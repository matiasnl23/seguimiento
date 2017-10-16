<?php

class Pedido {
  protected $db;

  protected $bas;
  protected $pedido;

  function __construct($db) {
    $this->db = $db;

    $this->pedido = [
      'pedidoID' => null,
      'bas' => null,
      'clienteID' => null,
      'contactoID' => null,
      'mail' => null,
      'titulo' => null,
      'descripcion' => null,
      'categoriaID' => null,
      'usuarioID' => null,
      'responsableID' => null,
      'proceso' => null,
      'estado' => null,

      'fecha_C' => null,
      'fecha_COT' => null,
      'fecha_AL' => null,
      'fecha_OC' => null,
      'fecha_EDIT' => null
    ];
  }

  public function buscar($id) {
    $q = $this->db->prepare('SELECT * FROM tblPedidos WHERE pedidoID = :pedidoID');
    $q->bindParam(':pedidoID', $id, PDO::PARAM_INT);

    if(!$q->execute())
      throw new JsonException($q->errorInfo(), 500, true);

    if($q->rowCount()<1)
      throw new JsonException("No se ha encontrado ningun pedido.", 404);

    $this->pedido = $q->fetch(PDO::FETCH_ASSOC);
  }

  public function setCliente($a) {
    // $a = strtoupper($a);
    if(is_null($a) || strlen($a)==0) {
      throw new JsonException("El campo cliente no puede estar vacío.", 400);
    }
    $this->pedido['clienteID'] = $a;
    $this->modificado();
  }
  public function setContacto($a) {
    // $a = strtoupper($a);
    if(is_null($a) || strlen($a)==0) {
      throw new JsonException("El campo contacto no puede estar vacío.", 400);
    }
    $this->pedido['contactoID'] = $a;
    $this->modificado();
  }
  public function setCategoria($a) {
    // $a = strtoupper($a);
    if(is_null($a) || strlen($a)==0) {
      throw new JsonException("El campo categoría no puede estar vacío.", 400);
    }
    $this->pedido['categoriaID'] = $a;
    $this->modificado();
  }

  public function setUsuario($a) {
    // $a = strtoupper($a);
    if(is_null($a) || strlen($a)==0) {
      throw new JsonException("Hay algún error relacionado con la identificación del usuario.", 400);
    }
    $this->pedido['usuarioID'] = $a;
    $this->modificado();
  }

  public function setTitulo($a) {
    $a = strtoupper($a);
    if(is_null($a) || strlen($a)==0) {
      throw new JsonException("El campo contacto no puede estar vacío.", 400);
    }
    $this->pedido['titulo'] = $a;
    $this->modificado();
  }
  public function setDescripcion($a) {
    // $a = strtoupper($a);
    if(is_null($a) || strlen($a)==0) {
      throw new JsonException("El campo descripción no puede estar vacío.", 400);
    }
    $this->pedido['descripcion'] = $a;
    $this->modificado();
  }

  public function setResponsable($a) {
    if(is_null($this->pedido['pedidoID']))
      throw new JsonException("No se ha declarado el pedido a cuál asignar el responsable.", 400);


    if(is_null($a) || strlen($a)==0 || !is_numeric($a))
      throw new JsonException("Ha ocurrido un problema al intentar designar el responsable.", 401);

    $this->pedido['responsableID'] = $a;

    if($this->pedido['proceso'] == 0)
      $this->pedido['proceso'] = 1;

    $q = $this->db->prepare('UPDATE tblPedidos SET responsableID = :responsableID, proceso = :proceso WHERE pedidoID = :pedidoID');
    $q->bindParam(':responsableID', $this->pedido['responsableID'], PDO::PARAM_INT);
    $q->bindParam(':proceso', $this->pedido['proceso'], PDO::PARAM_INT);
    $q->bindParam(':pedidoID', $this->pedido['pedidoID'], PDO::PARAM_INT);

    if(!$q->execute())
      throw new JsonException($q->errorInfo(), 500, true);
  }

  public function detalles() {
    $this->validar();

    return $this->pedido;
  }
  public function guardar() {
    $this->db->beginTransaction();

    if($this->pedido['pedidoID']) {
      $q = $this->db->prepare('UPDATE tblPedidos SET clienteID = :clienteID, contactoID = :contactoID, categoriaID = :categoriaID, titulo = :titulo, descripcion = :descripcion WHERE pedidoID = :pedidoID');

      $q->bindParam(':pedidoID', $this->pedido['pedidoID'], PDO::PARAM_INT);
    } else {
      $this->bas = new BAS($this->db);

      $this->pedido['bas'] = $this->bas->buscar('CA');
      $this->bas->incrementar();

      $q = $this->db->prepare('INSERT INTO tblPedidos (bas, usuarioID, clienteID, contactoID, categoriaID, titulo, descripcion) VALUES(:bas, :usuarioID, :clienteID, :contactoID, :categoriaID, :titulo, :descripcion)');

      $q->bindParam(':usuarioID', $this->pedido['usuarioID'], PDO::PARAM_INT);
      $q->bindParam(':bas', $this->pedido['bas'], PDO::PARAM_STR);
    }

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

  protected function validar() {
    // Validación del id del usuario.
    if(!isset($this->pedido['usuarioID']))
      throw new JsonException("El campo usuario no puede estar vacío.", 400);
    if(!is_numeric($this->pedido['usuarioID']))
      throw new JsonException("Hay algún error asociado al ingresar la información del usuario.", 500);

    // Validación del id del cliente.
    if(!isset($this->pedido['clienteID']))
      throw new JsonException("El campo cliente no puede estar vacío.", 400);
    if(!is_numeric($this->pedido['clienteID']))
      throw new JsonException("Hay algún error asociado al ingresar la información del cliente.", 500);

    // Validación del id del contacto.
    if(!isset($this->pedido['contactoID']))
      throw new JsonException("El campo contacto no puede estar vacío.", 400);
    if(!is_numeric($this->pedido['contactoID']))
      throw new JsonException("Hay algún error asociado al ingresar la información del contacto.", 500);

    // Validación del id de la categoría.
    if(!isset($this->pedido['categoriaID']))
      throw new JsonException("El campo categoria no puede estar vacío.", 400);
    if(!is_numeric($this->pedido['categoriaID']))
      throw new JsonException("Hay algún error asociado al ingresar la información del categoria.", 500);

    // Validación del título del pedido.
    if(!isset($this->pedido['titulo']))
      throw new JsonException("El campo titulo no puede estar vacío.", 400);
    if(!preg_match('/^[\w\sáéíóúÁÉÍÓÚ,.\-_+]+$/i',$this->pedido['titulo']))
      throw new JsonException("El título tiene caracteres no válidos.", 401);

    // Validación de la descripción del pedido.
    if(!isset($this->pedido['descripcion']))
      throw new JsonException("El campo descripción no puede estar vacío.", 400);

  }
  protected function modificado() {
    if($this->pedido['pedidoID'])
      $this->modificado = true;
  }

  public function activar() {
    if(is_null($this->pedido['pedidoID']))
      throw new JsonException("No se ha declarado el pedido a cuál asignar el estado.", 400);

    $this->pedido['estado'] = 1;

    $q = $this->db->prepare('UPDATE tblPedidos SET estado = :estado WHERE pedidoID = :pedidoID');
    $q->bindParam(':estado', $this->pedido['estado'], PDO::PARAM_INT);
    $q->bindParam(':pedidoID', $this->pedido['pedidoID'], PDO::PARAM_INT);

    if(!$q->execute())
      throw new JsonException($q->errorInfo(), 500, true);
  }
  public function cancelar() {
    if(is_null($this->pedido['pedidoID']))
      throw new JsonException("No se ha declarado el pedido a cuál asignar el estado.", 400);

    $this->pedido['estado'] = 2;

    $q = $this->db->prepare('UPDATE tblPedidos SET estado = :estado WHERE pedidoID = :pedidoID');
    $q->bindParam(':estado', $this->pedido['estado'], PDO::PARAM_INT);
    $q->bindParam(':pedidoID', $this->pedido['pedidoID'], PDO::PARAM_INT);

    if(!$q->execute())
      throw new JsonException($q->errorInfo(), 500, true);
  }
  public function posponer() {
    if(is_null($this->pedido['pedidoID']))
      throw new JsonException("No se ha declarado el pedido a cuál asignar el estado.", 400);

    $this->pedido['estado'] = 7;

    $q = $this->db->prepare('UPDATE tblPedidos SET estado = :estado WHERE pedidoID = :pedidoID');
    $q->bindParam(':estado', $this->pedido['estado'], PDO::PARAM_INT);
    $q->bindParam(':pedidoID', $this->pedido['pedidoID'], PDO::PARAM_INT);

    if(!$q->execute())
      throw new JsonException($q->errorInfo(), 500, true);
  }

  public function subir_cotizacion() {

  }
}

?>
