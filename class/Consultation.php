<?php

class Consultation
{
  /**
   * @param $id
   * @param $db
   * @return stdClass
   */
  public function get($id, $db = null): stdClass
  {
    if (is_null($db)):
      $db = new ConnectMAIN();
    endif;

    $stmt = $db->Prepare("SELECT * FROM tel_consulta WHERE con_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $db->setToObject($row);
  }

  /**
   * @param $id
   * @param $db
   * @return array
   */
  public function getHistory($id, $db = null): array
  {
    if (is_null($db)):
      $db = new ConnectMAIN();
    endif;

    $stmt = $db->Prepare("SELECT con_id FROM tel_consulta cv
                                    WHERE pac_id = ?
                                    ORDER BY con_id DESC");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $lista = [];

    while ($row = $result->fetch_assoc()) :
      $lista[] = $this->get($row['con_id']);
    endwhile;

    unset($db);
    return $lista;
  }

  /**
   * @param $db
   * @return stdClass
   */
  public function getWaiting($db = null): stdClass
  {
    if (is_null($db)):
      $db = new ConnectMAIN();
    endif;

    $stmt = $db->Prepare("SELECT con_id FROM tel_consulta cv WHERE sta_id = 1");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    unset($db);

    if (is_null($row)) {
      $obj = new stdClass();
      $obj->con_id = null;
      return $obj;
    } else {
      return $this->get($row['con_id']);
    }
  }

  /**
   * @param $pac
   * @param $ant
   * @param $card
   * @param $resp
   * @param $temp
   * @param $oxi
   * @param $pres
   * @param $gli
   * @param $db
   * @return array
   */
  public function set($pac, $ant, $card, $resp, $temp, $oxi, $pres, $gli, $db = null): array
  {
    if (is_null($db)):
      $db = new ConnectMAIN();
    endif;

    try {
      $stmt = $db->Prepare("INSERT INTO tel_consulta (pac_id, con_antecedentes, con_card, con_resp, con_temp, con_oxi, con_pres, con_gli)
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

      if (!$stmt) throw new Exception("La inserción de la consulta falló en su preparación.");

      $result = $db->prepareToBD(func_get_args());
      $bind = $stmt->bind_param($result['typeStr'], ...$result['params']);

      if (!$bind) throw new Exception("La inserción de la consulta falló en su binding.");
      if (!$stmt->execute()) throw new Exception("La inserción de la consulta falló en su ejecución.");

      $result = array('status' => true, 'conid' => $stmt->insert_id);
      $stmt->close();
      return $result;
    } catch (Exception $e) {
      return array('status' => false, 'msg' => $e->getMessage());
    }
  }

  /**
   * @param $status
   * @param $id
   * @param $db
   * @return array
   */
  public function setStatus($status, $id, $db = null): array
  {
    if (is_null($db)):
      $db = new ConnectMAIN();
    endif;

    try {
      $stmt = $db->Prepare("UPDATE tel_consulta SET sta_id = ? WHERE con_id = ?");

      if (!$stmt) throw new Exception("La actualización del estado de la consulta falló en su preparación.");

      $result = $db->prepareToBD(func_get_args());
      $bind = $stmt->bind_param($result['typeStr'], ...$result['params']);

      if (!$bind) throw new Exception("La actualización del estado de la consulta falló en su binding.");
      if (!$stmt->execute()) throw new Exception("La actualización del estado de la consulta falló en su ejecución.");

      $result = array('status' => true, 'conid' => $stmt->insert_id);
      $stmt->close();
      return $result;
    } catch (Exception $e) {
      return array('status' => false, 'msg' => $e->getMessage());
    }
  }
}
