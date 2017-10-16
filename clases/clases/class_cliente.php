<?php
/**
 * Clase para administración de la empresa cliente.
 */
class Cliente
{
  protected $db;

  protected $cliente;
  protected $modificado;

  function __construct($db) {
    $this->db = $db;

    $this->cliente = [
      "clienteID" => null,
      "nombre" => null,
      "domicilio" => null,
      "aliases" => null
    ];
    $this->modificado = false;
  }

  public function buscar($id) {
    $q = $this->db->prepare('SELECT * FROM tblClientes WHERE clienteID = :clienteID');
    $q->bindParam(':clienteID', $id, PDO::PARAM_INT);

    if(!$q->execute())
      throw new JsonException($q->errorInfo(), 500, true);

    if($q->rowCount()<1)
      throw new JsonException("No se ha encontrado ningun cliente.", 404);

    $this->cliente = $q->fetch(PDO::FETCH_ASSOC);
  }

  public function setNombre($a) {
    $a = strtoupper($a);
    if(is_null($a) || strlen($a)<2) {
      throw new JsonException("El campo nombre no puede estar vacío.", 400);
    }
    $this->cliente['nombre'] = $a;
    $this->modificado();
  }
  public function setDomicilio($a) {
    if(strlen($a)==0) { $a = null; }
    $this->cliente['domicilio'] = $a;
    $this->modificado();
  }
  public function setAliases($a) {
    $a = strtoupper($a);
    if(is_null($a) || strlen($a)<2) {
      throw new JsonException("El campo alias no puede estar vacío.", 400);
    }
    $this->cliente['aliases'] = $a;
    $this->modificado();
  }

  public function detalles() {
    return $this->cliente;
  }

  public function guardar() {

    $this->existente();

    if($this->cliente['clienteID']) {
      $q = $this->db->prepare('UPDATE tblClientes SET nombre = :nombre, domicilio = :domicilio, aliases = :aliases WHERE clienteID = :clienteID');
      $q->bindParam(':clienteID', $this->cliente['clienteID'], PDO::PARAM_INT);
    } else {
      $q = $this->db->prepare('INSERT INTO tblClientes (nombre, domicilio, aliases) VALUES(:nombre, :domicilio, :aliases)');
    }
    $q->bindParam(':nombre', $this->cliente['nombre'], PDO::PARAM_STR);
    $q->bindParam(':domicilio', $this->cliente['domicilio'], PDO::PARAM_STR);
    $q->bindParam(':aliases', $this->cliente['aliases'], PDO::PARAM_STR);

    $this->validar();

    if(!$q->execute())
      throw new JsonException($q->errorInfo(), 500, true);

    if(!$this->cliente['clienteID'])
      $this->cliente['clienteID'] = $this->db->lastInsertId();
  }

  public function existente() {
    if($this->modificado) {
      $q = $this->db->prepare('SELECT * FROM tblClientes WHERE nombre = :nombre AND clienteID != :clienteID');
      $q->bindParam(':clienteID', $this->cliente['clienteID'], PDO::PARAM_INT);
    } else {
      $q = $this->db->prepare('SELECT * FROM tblClientes WHERE nombre = :nombre');
    }
    $q->bindParam(':nombre', $this->cliente['nombre'], PDO::PARAM_STR);

    if(!$q->execute())
      throw new JsonException($q->errorInfo(), 500, true);

    if($q->rowCount()>0) {
      throw new JsonException("Ya existe otro cliente con ese nombre.", 501);
    }
  }

  public function validar() {
    // Validación del nombre de la empresa.
    if(!isset($this->cliente['nombre']))
      throw new JsonException("El campo nombre no puede estar vacíó.", 400);

    // Validación de los alias de la empresa.
    if(!isset($this->cliente['aliases']))
      throw new JsonException("El campo alias no puede estar vacíó.", 400);
  }
  public function modificado() {
    if($this->cliente['clienteID'])
      $this->modificado = true;
  }
}
?>
