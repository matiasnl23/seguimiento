<?php
/**
 * Clase para administración de las categorias.
 */
class Categoria {
  protected $db;

  protected $categoria;
  protected $modificado;

  function __construct($db) {
    $this->db = $db;

    $this->categoria = [
      "categoriaID" => null,
      "nombre" => null
    ];
    $this->modificado = false;
  }

  public function buscar($id) {
    $q = $this->db->prepare('SELECT * FROM tblCategorias WHERE categoriaID = :categoriaID');
    $q->bindParam(':categoriaID', $id, PDO::PARAM_INT);

    if(!$q->execute())
      throw new JsonException($q->errorInfo(), 500, true);

    if($q->rowCount()<1)
      throw new JsonException("No se ha encontrado ningun categoria.", 404);

    $this->categoria = $q->fetch(PDO::FETCH_ASSOC);
  }
  public function buscarTodos() {
    $q = $this->db->prepare('SELECT * FROM tblCategorias');
    if(!$q->execute())
      throw new JsonException($q->errorInfo(), 500, true);

    if($q->rowCount()<1)
      throw new JsonException("No se ha encontrado ninguna categoria.", 404);

    return $q->fetchAll(PDO::FETCH_ASSOC);
  }

  public function setNombre($a) {
    if(is_null($a) || strlen($a)<2) {
      throw new JsonException("El campo nombre no puede estar vacío.", 400);
    }
    $this->categoria['nombre'] = $a;
    $this->modificado();
  }

  public function detalles() {
    return $this->categoria;
  }

  public function guardar() {

    $this->existente();

    if($this->categoria['categoriaID']) {
      $q = $this->db->prepare('UPDATE tblCategorias SET nombre = :nombre WHERE categoriaID = :categoriaID');
      $q->bindParam(':categoriaID', $this->categoria['categoriaID'], PDO::PARAM_INT);
    } else {
      $q = $this->db->prepare('INSERT INTO tblCategorias (nombre) VALUES(:nombre)');
    }
    $q->bindParam(':nombre', $this->categoria['nombre'], PDO::PARAM_STR);

    $this->validar();

    if(!$q->execute())
      throw new JsonException($q->errorInfo(), 500, true);

    if(!$this->categoria['categoriaID'])
      $this->categoria['categoriaID'] = $this->db->lastInsertId();
  }

  public function existente() {
    if($this->modificado) {
      $q = $this->db->prepare('SELECT * FROM tblCategorias WHERE nombre = :nombre AND categoriaID != :categoriaID');
      $q->bindParam(':categoriaID', $this->categoria['categoriaID'], PDO::PARAM_INT);
    } else {
      $q = $this->db->prepare('SELECT * FROM tblCategorias WHERE nombre = :nombre');
    }
    $q->bindParam(':nombre', $this->categoria['nombre'], PDO::PARAM_STR);

    if(!$q->execute())
      throw new JsonException($q->errorInfo(), 500, true);

    if($q->rowCount()>0) {
      throw new JsonException("Ya existe otra categoria con ese nombre.", 501);
    }
  }

  public function validar() {
    // Validación del nombre de la empresa.
    if(!isset($this->categoria['nombre']))
      throw new JsonException("El campo nombre no puede estar vacíó.", 400);
    if(!preg_match(REGEX_CATEGORIA, $this->categoria['nombre']))
      throw new JsonException("El campo nombre tiene un formato inválido.", 401);
  }
  public function modificado() {
    if($this->categoria['categoriaID'])
      $this->modificado = true;
  }
}
?>
