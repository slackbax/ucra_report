<?php

$BASEDIR = explode('ajax', dirname(__FILE__))[0];
define("SAVE_FOLDER", $BASEDIR . 'upload');
require $BASEDIR . 'src/fn.php';
require $BASEDIR . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (extract($_POST)):
  try {
    $directories = glob($BASEDIR . '/upload/*', GLOB_ONLYDIR);
    foreach ($directories as $dir) {
      delTree($dir);
    }

    $folder = rand(1, 100000);
    if (file_exists(SAVE_FOLDER . '/' . $folder))
      rmdir(SAVE_FOLDER . '/' . $folder);
    mkdir(SAVE_FOLDER . '/' . $folder, 0755, true);

    if (!empty($_FILES)):
      foreach ($_FILES as $aux => $file):
        $tempFile = $file['tmp_name'][0];
        $targetFile = rtrim(SAVE_FOLDER, '/') . '/' . $folder . '/upload.csv';
        if (!move_uploaded_file($tempFile, $targetFile)) {
          throw new Exception('error de subida ' .$_FILES["file"]["error"]);
        }
      endforeach;
    endif;

    $reader = IOFactory::createReader('Csv');
    $sp = $reader->load($targetFile);
    $ws = $sp->getActiveSheet();
    $max_data_row = $ws->getHighestDataRow();
    $row_iterator = $ws->getRowIterator(1, $max_data_row);
    $row_num = 1;
    $data = [];

    foreach ($row_iterator as $row) {
      if ($row->isEmpty()) {
        continue;
      }

      $column_num = 1;
      $column_iterator = $row->getCellIterator('A', 'Z');
      $data_row = [];
      foreach ($column_iterator as $cell) {
        if ($cell->getValue() == null) {
          continue;
        }
        $data_row[$column_num-1] = preg_replace('/[^0-9a-zA-Z: \/()]/', '', removeAccents($cell->getValue()));
        $column_num++;
      }

      $data[] = $data_row;
      $row_num++;
    }
    $headers = $data[0];
    $data = array_slice($data, 1);

    $response = array('type' => true, 'msg' => 'OK', 'headers' => $headers, 'data' => $data, 'folder' => $folder);
    echo json_encode($response);
  } catch (Exception $e) {
    $response = array('status' => false, 'msg' => $e->getMessage());
    echo json_encode($response);
  }
endif;