<?php

class ConnectMAIN
{
  public ?mysqli $mysqli = null;

  public function __construct()
  {
    $this->mysqli = new mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, MAIN_DATABASE);
    $this->mysqli->set_charset(DB_CHARSET);
  }

  public function __destruct()
  {
    $this->mysqli->close();
  }

  public function autoCommit($mode): void
  {
    $this->mysqli->autocommit($mode);
  }

  public function Commit(): void
  {
    $this->mysqli->commit();
  }

  public function Rollback(): void
  {
    $this->mysqli->rollback();
  }

  public function Prepare($query): bool|mysqli_stmt
  {
    return $this->mysqli->prepare($query);
  }

  public function clearText($text): string
  {
    return $this->mysqli->real_escape_string(trim($text));
  }

  public function prepareToBD($args): array
  {
    $typeStr = '';
    $params = [];
    foreach ($args as $a):
      if (gettype($a) !== 'object')
        if (is_int($a) || ctype_digit((string)$a)):
          $typeStr .= 'i';
          $params[] = $a;
        elseif (is_double($a)):
          $typeStr .= 'd';
          $params[] = $a;
        elseif ($a === 'true' or $a === true):
          $typeStr .= 'i';
          $params[] = 1;
        elseif ($a === 'false' or $a === false):
          $typeStr .= 'i';
          $params[] = 0;
        elseif (empty($a)):
          $typeStr .= 's';
          $params[] = null;
        else:
          $typeStr .= 's';
          $params[] = $this->clearText($a);
        endif;
    endforeach;

    return array('typeStr' => $typeStr, 'params' => $params);
  }

  public function setToObject($args): stdClass
  {
    $obj = new stdClass();
    foreach ($args as $key => $value) {
      if (is_null($value)) {
        $obj->{$key} = '';
      } else {
        $obj->{$key} = $value;
      }
    }
    return $obj;
  }
}
