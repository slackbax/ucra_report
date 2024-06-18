<?php

/**
 * @param $username
 * @param $whitelist
 * @return void
 * @throws Exception
 */
function check_access($username, $whitelist): void
{
  if (!in_array($username, $whitelist)) throw new Exception("La plataforma se encuentra en mantención en este momento. Por favor, intente nuevamente más tarde. Gracias por su paciencia.", 1);
}

/**
 * @param $length
 * @return string
 * @throws \Random\RandomException
 */
function random_string($length = 4): string
{
  if ($length < 4) $length = 4;
  return bin2hex(random_bytes(($length - ($length % 2)) / 2));
}

/**
 * @param $length
 * @return string
 * @throws \Random\RandomException
 */
function generate_token($length): string
{
  if ($length < 4) $length = 4;
  return bin2hex(random_bytes(($length - ($length % 2)) / 2));
}

/**
 * @param $created_at
 * @param $exam
 * @return string
 */
function exam_code($created_at, $exam): string
{
  return date_format(date_create($created_at), 'ym') . str_pad($exam, 4, '0', STR_PAD_LEFT);
}

/**
 * @param $date
 * @return string
 */
function db_date($date): string
{
  $aux = explode('/', $date);
  return $aux[2] . '-' . $aux[1] . '-' . $aux[0];
}

/**
 * @param $date
 * @return string
 */
function formatter_date($date): string
{
  $aux = explode('-', $date);
  return $aux[2] . '/' . $aux[1] . '/' . $aux[0];
}

/**
 * @param $time
 * @return string
 */
function formatter_time($time): string
{
  return substr($time, 0, 5);
}

/**
 * @param $date
 * @return string
 */
function formatter_date_hour($date): string
{
  $aux1 = explode(' ', $date);
  $aux2 = explode('-', $aux1[0]);
  return $aux2[2] . '/' . $aux2[1] . '/' . $aux2[0] . ' ' . $aux1[1];
}

/**
 * @param $document
 * @param $is_rut
 * @return string
 */
function fm_document($document, $is_rut): string
{
  $aux = ($is_rut == 1) ? 'RUT ' : 'PP ';
  return $aux . $document;
}

/**
 * @param $fullname
 * @param $sex
 * @return string
 */
function fm_abbreviation($fullname, $sex): string
{
  $aux = ($sex == 1) ? 'DR. ' : 'DRA. ';
  return $aux . $fullname;
}

/**
 * @param $birth
 * @param $exam_at
 * @param $type
 * @return mixed|string|void|null
 */
function patient_age($birth, $exam_at = null, $type = null)
{
  if (empty($birth)) return null;
  $birth = date_create($birth);
  $date = (is_null($exam_at)) ? date_create() : date_create($exam_at);
  $diff = date_diff($birth, $date);
  if ($type == 'short') {
    $years = $diff->y . 'a';
    $months = $diff->m . 'm';
    $days = $diff->d . 'd';
  } else {
    $years = ($diff->y == 1) ? $diff->y . ' año' : $diff->y . ' años';
    $months = ($diff->m == 1) ? $diff->m . ' mes' : $diff->m . ' meses';
    $days = ($diff->d == 1) ? $diff->d . ' dia' : $diff->d . ' días';
  }
  $array = [];
  if ($diff->y > 0) $array[] = $years;
  if ($diff->m > 0) $array[] = $months;
  if ($diff->y < 1 and $diff->d > 0) $array[] = $days;
  if (count($array) == 0) return 'Nació hace unas horas';
  if (count($array) == 1) return $array[0];
  if (count($array) == 2 && $type == 'short') return $array[0] . ' ' . $array[1];
  if (count($array) == 2 && is_null($type)) return $array[0] . ' y ' . $array[1];
}
