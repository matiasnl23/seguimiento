<?php
/**
 * Clase para administración de la empresa cliente.
 */
class Contacto {
  protected $db;

  protected $contacto;
  protected $modificado;

  function __construct($db) {
    $this->db = $db;

    $this->contacto = [
      "contactoID" => null,
      "clienteID" => null,
      "nombre" => null,
      "mail" => null,
      "celular" => null,
      "fijo" => null,
      "interno" => null
    ];
    $this->modificado = false;
  }

  public function buscar($id) {
    $q = $this->db->prepare('SELECT * FROM tblContactos WHERE contactoID = :contactoID');
    $q->bindParam(':contactoID', $id, PDO::PARAM_INT);

    if(!$q->execute())
      throw new JsonException($q->errorInfo(), 500, true);

    if($q->rowCount()<1)
      throw new JsonException("No se ha encontrado ningun contacto.", 404);

    $this->contacto = $q->fetch(PDO::FETCH_ASSOC);
  }
  public function buscarTodos() {
    $q = $this->db->prepare('SELECT * FROM tblContactos');
    if(!$q->execute())
      throw new JsonException($q->errorInfo(), 500, true);

    if($q->rowCount()<1)
      throw new JsonException("No se ha encontrado ningun contacto.", 404);

    return $q->fetchAll(PDO::FETCH_ASSOC);
  }

  public function setCliente($a) {
    $this->contacto['clienteID'] = $a;
    $this->modificado();
  }
  public function setNombre($a) {
    $a = ucwords(strtolower($a));
    if(is_null($a) || strlen($a)<2) {
      throw new JsonException("El campo nombre no puede estar vacío.", 400);
    }
    $this->contacto['nombre'] = $a;
    $this->modificado();
  }
  public function setMail($a) {
    $a = strtolower($a);
    $this->contacto['mail'] = $a;
    $this->modificado();
  }
  public function setCelular($a) {
    if(strlen($a)==0) { $a = null; }
    $this->contacto['celular'] = $a;
    $this->modificado();
  }
  public function setFijo($a) {
    if(strlen($a)==0) { $a = null; }
    $this->contacto['fijo'] = $a;
    $this->modificado();
  }
  public function setInterno($a) {
    if(strlen($a)==0) { $a = null; }
    $this->contacto['interno'] = $a;
    $this->modificado();
  }

  public function detalles() {
    return $this->contacto;
  }

  public function guardar() {

    $this->existente();

    if($this->contacto['contactoID']) {
      $q = $this->db->prepare('UPDATE tblContactos SET clienteID = :clienteID, nombre = :nombre, mail = :mail, celular = :celular, fijo = :fijo, interno = :interno WHERE contactoID = :contactoID');
      $q->bindParam(':contactoID', $this->contacto['contactoID'], PDO::PARAM_INT);
    } else {
      $q = $this->db->prepare('INSERT INTO tblContactos (clienteID, nombre, mail, celular, fijo, interno) VALUES(:clienteID, :nombre, :mail, :celular, :fijo, :interno)');
    }
    $q->bindParam(':clienteID', $this->contacto['clienteID'], PDO::PARAM_INT);
    $q->bindParam(':nombre', $this->contacto['nombre'], PDO::PARAM_STR);
    $q->bindParam(':mail', $this->contacto['mail'], PDO::PARAM_STR);
    $q->bindParam(':celular', $this->contacto['celular'], PDO::PARAM_STR);
    $q->bindParam(':fijo', $this->contacto['fijo'], PDO::PARAM_STR);
    $q->bindParam(':interno', $this->contacto['interno'], PDO::PARAM_STR);

    $this->validar();

    if(!$q->execute())
      throw new JsonException($q->errorInfo(), 500, true);

    if(!$this->contacto['contactoID'])
      $this->contacto['contactoID'] = $this->db->lastInsertId();
  }

  public function existente() {
    if($this->modificado) {
      $q = $this->db->prepare('SELECT * FROM tblContactos WHERE mail = :mail AND contactoID != :contactoID');
      $q->bindParam(':contactoID', $this->contacto['contactoID'], PDO::PARAM_INT);
    } else {
      $q = $this->db->prepare('SELECT * FROM tblContactos WHERE mail = :mail');
    }
    $q->bindParam(':mail', $this->contacto['mail'], PDO::PARAM_STR);

    if(!$q->execute())
      throw new JsonException($q->errorInfo(), 500, true);

    if($q->rowCount()>0) {
      throw new JsonException("Ya existe otro contacto con esa dirección de mail.", 501);
    }
  }

  public function validar() {
    // Validación del id del cliente.
    if(!isset($this->contacto['clienteID']))
      throw new JsonException("El campo cliente no puede estar vacío.", 400);
    if(!is_numeric($this->contacto['clienteID']))
      throw new JsonException("Hay algún error asociado al emparejar el cliente con el contacto.", 500);

    // Validación del nombre del contacto.
    if(!isset($this->contacto['nombre']))
      throw new JsonException("El campo nombre no puede estar vacíó.", 400);

    $nombre_validado = false;
    foreach (REGEX_NOMBRE as $v) {
      if(preg_match($v, $this->contacto['nombre']))
        $nombre_validado = true;
    }

    if(!$nombre_validado)
      throw new JsonException("El formato del campo nombre no coincide con los formatos aceptados." . $nombre_validado, 401);


    // Validación del mail del contacto.
    if(!isset($this->contacto['mail']) || strlen($this->contacto['mail'])==0)
      throw new JsonException("El campo mail no puede estar vacíó.", 400);
    if(!filter_var($this->contacto['mail'], FILTER_VALIDATE_EMAIL))
      throw new JsonException("El campo mail tiene un formato inválido.", 401);

    // ACA INGRESAR VALIDACIÓN DE LOS TELÉFONOS //

    // Validación del interno del contacto.
    if(!preg_match('/^\d{0,6}$/', $this->contacto['interno']))
      throw new JsonException("Ha excedido el máximo de caracteres que puede tener este campo.", 401);

  }
  public function modificado() {
    if($this->contacto['contactoID'])
      $this->modificado = true;
  }
}
?>
