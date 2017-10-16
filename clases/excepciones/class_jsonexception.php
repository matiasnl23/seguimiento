<?php
/**
 *
 */
class JsonException extends Exception
{

  function __construct($msg, $id, $sql = false)
  {
    if($sql) {
      parent::__construct('SQL error: '.$msg[2], $id);
      return;
    }
    parent::__construct($msg, $id);
  }

  public function getJson()
  {
    echo json_encode([
      "mensaje"=> $this->getMessage(),
      "code"=> $this->getCode(),
      "extra"=> $this->getTrace()
    ]);
  }
}

?>
