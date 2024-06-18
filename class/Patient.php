<?php

class Patient
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

    $stmt = $db->Prepare("SELECT * 
                                    FROM tel_paciente
                                    WHERE pac_id = ?");

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    return $db->setToObject($row);
  }

  /**
   * @param $rut
   * @param $db
   * @return stdClass
   */
  public function getByRut($rut, $db = null): stdClass
  {
    if (is_null($db)):
      $db = new ConnectMAIN();
    endif;

    $stmt = $db->Prepare("SELECT pac_id 
                                    FROM tel_paciente
                                    WHERE pac_doc = ?");

    $stmt->bind_param("s", $rut);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    unset($db);

    if (isset($row['pac_id'])) {
      return $this->get($row['pac_id']);
    } else {
      $obj = new stdClass();
      $obj->pac_id = null;
      return $obj;
    }
  }

  /**
   * @param $doc
   * @param $isrut
   * @param $name
   * @param $ap
   * @param $am
   * @param $sex
   * @param $nac
   * @param $db
   * @return array
   */
  public function set($doc, $isrut, $name, $ap, $am, $sex, $nac, $db = null): array
  {
    if (is_null($db)):
      $db = new ConnectMAIN();
    endif;

    try {
      $stmt = $db->Prepare("INSERT INTO tel_paciente (pac_doc, pac_isrut, pac_nombre, pac_ap, pac_am, pac_sex, pac_nacimiento)
                                    VALUES (?, ?, UPPER(?), UPPER(?), UPPER(?), ?, ?)");

      if (!$stmt):
        throw new Exception("La inserción del paciente falló en su preparación.");
      endif;

      $result = $db->prepareToBD(func_get_args());
      $bind = $stmt->bind_param($result['typeStr'], ...$result['params']);

      if (!$bind):
        throw new Exception("La inserción del paciente falló en su binding.");
      endif;

      if (!$stmt->execute()):
        throw new Exception("La inserción del paciente falló en su ejecución.");
      endif;

      $result = array('status' => true, 'msg' => $stmt->insert_id);
      $stmt->close();
      return $result;
    } catch (Exception $e) {
      return array('status' => false, 'msg' => $e->getMessage());
    }
  }

  /**
   * @param $id
   * @param $med
   * @param $us_upd
   * @param $status
   * @param $db
   * @return array
   */
  public function update($med, $us_upd, $status, $id, $db = null): array
  {
    if (is_null($db)):
      $db = new ConnectMAIN();
    endif;

    try {
      $stmt = $db->Prepare("UPDATE tel_paciente SET med_id = ?, pat_updateat = CURRENT_TIMESTAMP, us_update_id = ?, pat_status = ? WHERE pat_id = ?");

      if (!$stmt):
        throw new Exception("La actualización del paciente falló en su preparación.");
      endif;

      $result = $db->prepareToBD(func_get_args());
      $bind = $stmt->bind_param($result['typeStr'], ...$result['params']);

      if (!$bind):
        throw new Exception("La actualización del paciente falló en su binding.");
      endif;

      if (!$stmt->execute()):
        throw new Exception("La actualización del paciente falló en su ejecución.");
      endif;

      $result = array('estado' => true, 'msg' => $stmt->insert_id);
      $stmt->close();
      return $result;
    } catch (Exception $e) {
      return array('estado' => false, 'msg' => $e->getMessage());
    }
  }
}
