<?php
$random_nums = "(";
  for($i=0;$i<200;$i++) {
    $random_nums .= rand(0,10000);
    if ($i < 199) {
      $random_nums .= ",";
    }
  }
  $random_nums .= ")";
  
  $post_body = array(
    'endpoint'=>'games',
    'fields'=>'cover.url',
    'limit'=>'10',
    'where'=>'cover.url != null & id = '.$random_nums
  );
  $fields = "";
  foreach ($post_body as $key => $value) {
    if ($key == 'endpoint') $igdb_url .= $value;
    else {
      $fields .= "$key $value;";
    }
  }
  $curl = curl_init();
  curl_setopt_array($curl, [
    CURLOPT_URL => $igdb_url,
    CURLOPT_HTTPHEADER => array("Client-ID: $igdb_client_id",
                   "Authorization: Bearer $igdb_access_token",
                   "Accept: application/json"),
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => $fields,
    CURLOPT_RETURNTRANSFER => 1
  ]);
  $header_imgs = curl_exec($curl);
  curl_close($curl);
  ?>