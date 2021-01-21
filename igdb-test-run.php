<?php
  $client_id = 'gn969nmh00hd9nxtuawgxinki7rop7';
  $access_token = 'bu6p9wzid55nv8kslefy1d39akawil';
  $post_body = array(
    'endpoint'=>'characters',
    'fields'=>'name,description,mug_shot.url,games.name,games.cover.url,games.artworks.url,games.game_modes.name,games.game_engines.name,games.game_engines.description,games.game_engines.logo.url,games.franchise.name,games.franchises.name,games.genres.name,games.involved_companies.company.name, games.involved_companies.company.description, games.involved_companies.company.logo.url',
    'limit'=>'20',
    'where'=>'description != null & id > '.rand(0,1000),
    'sort'=>'id asc'
  );
  $url = "https://api.igdb.com/v4/";
  $fields = "";
  foreach ($post_body as $key => $value) {
    if ($key == 'endpoint') $url .= $value;
    else {
      $fields .= "$key $value;";
    }
  }
  $curl = curl_init();
  curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_HTTPHEADER => array("Client-ID: $client_id",
                   "Authorization: Bearer $access_token",
                   "Accept: application/json"),
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => $fields,
    CURLOPT_RETURNTRANSFER => 1
  ]);
  $result = curl_exec($curl);
  ?>
  <!DOCTYPE html>
  <html>
  <head>
  <link rel="stylesheet" type="text/css" href="/css/style.css?v=1">
  <style>
  .results-grid {word-break:break-word;}
  @media screen and (min-width:500px) {
    .results-grid {
      display:grid;
      grid-template-columns:auto auto;
    }
  }
  </style>
  </head>
  <body style="text-align:left;">
  <h1 style="text-align:center;">SEARCH API</h1><hr>
  <?php
  echo "<div class='results-grid'>";
  echo read_out_json_result(json_decode($result,true),2);
  echo "</div>";
  curl_close($curl);
  
  
  function read_out_json_result($json, $nest_level) {
    $result = "";
    if (empty($json[0])) $result .= "<dl style='box-sizing:border-box;border:1px dashed;margin:2px;padding:4px;'>";
    else {
      $result .= "<ol style='display:contents'>";
    }
    if (!empty($json['name'])) $result .= "<h$nest_level>$json[name]</h$nest_level>"; else $nest_level--;
    if (!empty($json['mug_shot']['url'])) $result .= '<img src="'.$json['mug_shot']['url'].'">';
    if (!empty($json['cover']['url'])) $result .= '<img src="'.$json['cover']['url'].'">';
    foreach ($json as $key => $value) {
      $v = $value;
      if (is_array($value)) $v = read_out_json_result($value,$nest_level+1);
      else if (is_array(json_decode($value,true))) $v = read_out_json_result(json_decode($value,true),$nest_level+1);
      else if ($key == 'url') $v = "<img src='$value'>";
      if (empty($json[0])) $result .= "<dt>$key</dt><dd>$v</dd>";
      else $result .= "<li style='display:contents;'>$v</li>";
    }if (empty($json[0])) $result .= "</dl>"; else $result .= "</ol>";
    return $result;
  }
?></body>
</html>