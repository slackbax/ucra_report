<?php

function format_array($arr): array
{
  array_unshift($arr, null);
  $arr[] = null;
  return $arr;
}

function format_dates_array($arr): array
{
  $formatted_array = [];
  foreach ($arr as $date) {
    $d = explode('-', $date);
    $formatted_array[] = $d[2] . '-' . $d[1] . '-' . $d[0];
  }
  return $formatted_array;
}

$BASEDIR = explode('ajax', dirname(__FILE__))[0];
define("SAVE_FOLDER", $BASEDIR . 'upload');
require $BASEDIR . 'src/fn.php';
require $BASEDIR . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

if (extract($_POST)):
  try {
    $targetFile = rtrim(SAVE_FOLDER, '/') . '/' . $folder . '/upload.csv';
    $reader = IOFactory::createReader('Csv');
    $ss = new Spreadsheet();
    $sheet = $ss->getActiveSheet();
    $sp = $reader->load($targetFile);
    $ws = $sp->getActiveSheet();
    $max_data_row = $ws->getHighestDataRow();
    $full_data = [];
    $dates = [];
    $systolic = [];
    $diastolic = [];
    $average = [];
    $pulse = [];

    $row_iterator = $ws->getRowIterator(2, $max_data_row);
    $row_num = 1;

    $sheet->setCellValue('A1', 'Date');
    $sheet->setCellValue('B1', 'Systolic');
    $sheet->setCellValue('C1', 'Diastolic');
    $sheet->setCellValue('D1', 'Pulse');

    foreach ($row_iterator as $row) {
      if ($row->isEmpty()) {
        continue;
      }

      $column_num = 1;
      $column_iterator = $row->getCellIterator('A', 'Z');
      foreach ($column_iterator as $cell) {
        if ($cell->getValue() == null) {
          continue;
        }

        switch ($column_num) {
          case $date_sel + 1:
            $date_tmp = preg_replace("/[^0-9\-\/ ]/", '', $cell->getValue());
            if ($date_tmp == null or $date_tmp == '')
              continue 2;
            if (str_contains($date_tmp, ' '))
              $date_tmp = explode(' ', $date_tmp)[0];
            $date_tmp = str_replace('/', '-', $date_tmp);
            $date = $date_tmp;

            $sheet->setCellValue('A' . ($row_num + 1), $date);
            break;
          case $systolic_sel + 1:
            $sys = preg_replace('/[^0-9]/', '', $cell->getValue());
            $sheet->setCellValue('B' . ($row_num + 1), $sys);
            break;
          case $diastolic_sel + 1:
            $dia = preg_replace('/[^0-9]/', '', $cell->getValue());
            $sheet->setCellValue('C' . ($row_num + 1), $dia);
            break;
          case $pulse_sel + 1:
            $pul = preg_replace('/[^0-9]/', '', $cell->getValue());
            $sheet->setCellValue('D' . ($row_num + 1), $pul);
            break;
          default:
            break;
        }
        $column_num++;
      }

      $full_data[$date . '|' . $row_num] = ['systolic' => $sys, 'diastolic' => $dia, 'pulse' => $pul];
      $row_num++;
    }

    $writer = new Csv($ss);
    $writer->save(rtrim(SAVE_FOLDER, '/') . '/' . $folder . '/data.csv');

    ksort($full_data);

    foreach ($full_data as $date => $data) {
      $dates[] = explode('|', $date)[0];
      $systolic[] = $data['systolic'];
      $diastolic[] = $data['diastolic'];
      $pulse[] = $data['pulse'];
    }

    $raw_dates = format_dates_array($dates);
    $systolic = array_map('intval', $systolic);
    $diastolic = array_map('intval', $diastolic);
    $pulse = array_map('intval', $pulse);

    foreach ($systolic as $i => $sys) {
      $average[] = round(($sys / 3) + (2 * $diastolic[$i] / 3), 2);
    }

    $stats = [
      'dates' => [
        'data' => format_array($raw_dates),
        'min' => $dates[0],
        'max' => $dates[count($dates) - 1]
      ],
      'systolic' => [
        'data' => format_array($systolic),
        'mid' => mid($systolic),
        'median' => median($systolic),
        'max' => max($systolic),
        'min' => min($systolic),
        'sta_dev' => standard_deviation($systolic)
      ],
      'diastolic' => [
        'data' => format_array($diastolic),
        'mid' => mid($diastolic),
        'median' => median($diastolic),
        'max' => max($diastolic),
        'min' => min($diastolic),
        'sta_dev' => standard_deviation($diastolic)
      ],
      'avg' => format_array($average),
      'pulse' => [
        'data' => $pulse,
        'mid' => mid($pulse),
        'median' => median($pulse),
        'max' => max($pulse),
        'min' => min($pulse),
        'sta_dev' => standard_deviation($pulse)
      ]
    ];

    # FOLDER - DATE - SYSTOLIC - DIASTOLIC - PULSE
    $comm = "python ../src/ucra.py " . $folder;
    $output = exec($comm);

    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'Letter', true, 'UTF-8', false);

    $pdf->SetCreator('CRTBiobío');
    $pdf->SetAuthor('CRTBiobío');
    $pdf->SetTitle('Evaluación UCRA');
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetMargins(20, 15, 20);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->setFontSubsetting();
    $pdf->setJPEGQuality(75);

    $pdf->AddPage();
    $pdf->Image('../dist/img/logo_crt.png', 15, 15, 25, '', 'PNG');
    $html = <<<EOD
<h2>Reporte de Telemonitoreo UCRA</h2>
EOD;

    $pdf->SetFont('freesans', 'B', 13, '', true);
    $pdf->writeHTMLCell(0, 0, 15, 40, $html, 0, 1, 0, '', 'C');
    $pdf->ln(4);

    $pdf->SetFont('freesans', '', 13, '', true);
    $html = <<<EOD
<p style="line-height:22px">A continuación, se documentan los principales descriptores estadísticos y gráficos generados a partir de los datos obtenidos con el dispositivo Omron
HEM-9200T, monitor de presión arterial (PA) en el marco del proyecto de Telemonitoreo UCRA.</p>
EOD;
    $pdf->writeHTMLCell(0, 0, 15, '', $html, 0, 1, 0, '', 'J');

    $pdf->ln(8);
    $start_date = getArrayDate($stats['dates']['min']);
    $start_string = $start_date['day_w'] . ' ' . $start_date['day'] . ' de ' . $start_date['month_w'] . ' de ' . $start_date['year'];

    $end_date = getArrayDate($stats['dates']['max']);
    $end_string = $end_date['day_w'] . ' ' . $end_date['day'] . ' de ' . $end_date['month_w'] . ' de ' . $end_date['year'];

    $html = <<<EOD
<p style="line-height:22px"><strong>Período de mediciones</strong><br>$start_string al $end_string</p>
EOD;

    $pdf->writeHTMLCell(0, 0, 15, '', $html, 0, 1, 0);
    $pdf->ln(6);

    $pdf->SetFont('freesans', '', 12, '', true);
    $tbl = <<<EOD
<style>
tr{text-align:center}
td{height:28px;line-height:25px;}
.header{background-color:#325285;color:white}
.odd{background-color:#d9d9d9}
</style>
<table border="1" cellspacing="0" cellpadding="1">
    <tr>
        <td class="header">Variable fisiológica</td>
        <td class="header"><div style="font-size:5.5pt">&nbsp;</div>Máximo</td>
        <td class="header"><div style="font-size:5.5pt">&nbsp;</div>Mínimo</td>
        <td class="header"><div style="font-size:5.5pt">&nbsp;</div>Media</td>
        <td class="header"><div style="font-size:5.5pt">&nbsp;</div>Mediana</td>
        <td class="header">Desviación estándar</td>
    </tr>
    <tr>
        <td><strong>PA Sistólica</strong></td>
        <td>{$stats['systolic']['max']}</td>
        <td>{$stats['systolic']['min']}</td>
        <td>{$stats['systolic']['mid']}</td>
        <td>{$stats['systolic']['median']}</td>
        <td>{$stats['systolic']['sta_dev']}</td>
    </tr>
    <tr>
        <td class="odd"><strong>PA Diastólica</strong></td>
        <td class="odd">{$stats['diastolic']['max']}</td>
        <td class="odd">{$stats['diastolic']['min']}</td>
        <td class="odd">{$stats['diastolic']['mid']}</td>
        <td class="odd">{$stats['diastolic']['median']}</td>
        <td class="odd">{$stats['diastolic']['sta_dev']}</td>
    </tr>
    <tr>
        <td><strong>Frecuencia cardíaca</strong></td>
        <td><div style="font-size:5.5pt">&nbsp;</div>{$stats['pulse']['max']}</td>
        <td><div style="font-size:5.5pt">&nbsp;</div>{$stats['pulse']['min']}</td>
        <td><div style="font-size:5.5pt">&nbsp;</div>{$stats['pulse']['mid']}</td>
        <td><div style="font-size:5.5pt">&nbsp;</div>{$stats['pulse']['median']}</td>
        <td><div style="font-size:5.5pt">&nbsp;</div>{$stats['pulse']['sta_dev']}</td>
    </tr>
</table>
EOD;

    $pdf->setX(15);
    $pdf->writeHTML($tbl);

    $pdf->ln();
    $pdf->SetFont('freesans', '', 13, '', true);
    $html = <<<EOD
<p style="line-height:22px">A partir de los datos obtenidos, se puede observar la siguiente evolución de presión arterial:</p>
EOD;
    $pdf->writeHTMLCell(0, 0, 15, '', $html, 0, 1, 0, '', 'J');

    $pdf->ln();
    $pdf->setX(15);
    $pdf->Image('../upload/' . $folder . '/BP_plot.png', 15, $pdf->GetY(), 175, '', 'PNG');

    $pdf->AddPage();
    $pdf->Image('../dist/img/logo_crt.png', 15, 15, 25, '', 'PNG');

    $html = <<<EOD
<p style="line-height:22px">A partir de los datos obtenidos, se puede observar la siguiente evolución de frecuencia cardíaca:</p>
EOD;
    $pdf->writeHTMLCell(0, 0, 15, 40, $html, 0, 1, 0, '', 'J');

    $pdf->ln();
    $pdf->setX(15);
    $pdf->Image('../upload/' . $folder . '/HR_plot.png', 15, $pdf->GetY(), 175, '', 'PNG');

    $pdf->Output(SAVE_FOLDER . '/' . $folder . '/reporte_ucra.pdf', 'F');

    $response = array('type' => true, 'msg' => 'OK', 'stats' => $stats, 'url' => 'upload/' . $folder . '/reporte_ucra.pdf');
    echo json_encode($response);
  } catch (Exception $e) {
    $response = array('status' => false, 'msg' => $e->getMessage());
    echo json_encode($response);
  }
endif;