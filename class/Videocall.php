<?php

class Videocall
{
  public function getRoom($room_id): array
  {
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.videosdk.live/v2/rooms/" . $room_id,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER => array(
        'Authorization: ' . API_TOKEN,
        'Content-Type: application/json'
      ),
      CURLOPT_FAILONERROR => true,
      //comentar en produccion
      CURLOPT_SSL_VERIFYPEER => false
    ));
    $response = curl_exec($curl);

    if (curl_errno($curl)) {
      return ['error' => true, 'msg' => 'Error al obtener la sala. ' . curl_error($curl)];
    }

    curl_close($curl);
    return json_decode($response, true);
  }

  public function disableRoom($room_id): array
  {
    $curl = curl_init();
    $data = array(
      "roomId" => $room_id,
    );
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.videosdk.live/v2/rooms/deactivate",
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_HTTPHEADER => array(
        'Authorization: ' . API_TOKEN,
        'Content-Type: application/json'
      ),
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_FAILONERROR => true,
      //comentar en produccion
      CURLOPT_SSL_VERIFYPEER => false
    ));
    $response = curl_exec($curl);

    if (curl_errno($curl)) {
      return ['error' => true, 'msg' => 'Error al deshabilitar la sala. ' . curl_error($curl)];
    }

    curl_close($curl);
    return json_decode($response, true);
  }
}