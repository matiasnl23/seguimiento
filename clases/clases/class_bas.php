<?php
/**
 * Clase para administración de la empresa cliente.
 */
class BAS {
  protected $db;

  protected $bas;

  function __construct($db) {
    $this->db = $db;

    $this->bas = [
      "tagID" => null,
      "inicial" => null,
      "anual" => null,
      "medio" => null,
      "incremental" => null,
    ];
  }

  public function buscar($area) {
    $q = $this->db->prepare('SELECT * FROM tblTags WHERE medio = :medio');
    $q->bindParam(':medio', $area, PDO::PARAM_STR);

    if(!$q->execute())
      throw new JsonException($q->errorInfo(), 500, true);

    if($q->rowCount()<1)
      throw new JsonException("No se ha encontrado ningun BAS con esa identifiación.", 404);

    $this->bas = $q->fetch(PDO::FETCH_ASSOC);

    $this->anual();

    return $this->detalles();
  }
  public function incrementar() {
    $incremental = $this->bas['incremental'] + 1;

    $q = $this->db->prepare('UPDATE tblTags SET incremental = :incremental WHERE medio = :medio');
    $q->bindParam(':medio', $this->bas['medio'], PDO::PARAM_STR);
    $q->bindParam(':incremental', $incremental, PDO::PARAM_INT);

    if(!$q->execute())
      throw new JsonException($q->errorInfo(), 500, true);
  }
  public function anual() {
    if($this->bas['anual'] >= date('y')) {
      return;
    }

    $this->bas['anual'] = date('y');
    $this->bas['incremental'] = 0;

    $q = $this->db->prepare('UPDATE tblTags SET anual = :anual, incremental = :incremental WHERE medio = :medio');
    $q->bindParam(':anual', $this->bas['anual'], PDO::PARAM_INT);
    $q->bindParam(':incremental', $this->bas['incremental'], PDO::PARAM_INT);
    $q->bindParam(':medio', $this->bas['medio'], PDO::PARAM_STR);

    if(!$q->execute())
      throw new JsonException($q->errorInfo(), 500, true);
  }

  public function detalles() {
    $bas = $this->bas['inicial'].$this->bas['anual'].$this->bas['medio'].'-'.str_pad($this->bas['incremental'], 4, '0', STR_PAD_LEFT);
    return $bas;
  }
}
?>
