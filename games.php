<?php
  function paginate($page,$offset) {
    $search_html = '';
    if (isset($_GET['search'])) $search_html = "search=$_GET[search]&";
    if ($offset > 1) echo '<a href="?'.$search_html.'page=1">FIRST PAGE</a> ';
    if ($offset > 0) echo '<a href="?'.$search_html.'page='.($page-1).'">PREVIOUS PAGE</a> ';
    echo '<a href="?'.$search_html.'page='.($page+1).'">NEXT PAGE</a>';
    if (strlen($search_html) > 0) echo '<br><a href="/games/">CLEAR SEARCH</a>';
  }

  $page = 1;
  $offset = 0;
  if (isset($_GET['page'])) {
    $page = $_GET['page'];
    $offset = max(0,($_GET['page']-1) * 30);
  }

  $client_id = 'gn969nmh00hd9nxtuawgxinki7rop7';
  $access_token = 'bu6p9wzid55nv8kslefy1d39akawil';
  $post_body = array(
    'endpoint'=>'games',
    'fields'=>'name,alternative_names.name,cover.url',
    'limit'=>'30',
    'offset' => $offset,
    'sort' => 'name'
  );
  if (isset($_GET['search'])) {
    $s_term = str_replace('+',' ',str_replace('%20',' ',$_GET['search']));
    $post_body['where'] = 'name ~ *"'.$s_term.'"* | alternative_names.name ~ *"'.$s_term.'"*';
  }
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
  <title><?php if (isset($_GET['search'])) echo "Searching for: $_GET[search]"; else echo 'Games'; ?></title>
  <link rel="stylesheet" href="/css/style.css" type="text/css">
  <link rel="shortcut icon" type="image/png" href="/img/favicon.png"/>
  <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
  <h1><?php if (isset($_GET['search'])) echo "Searching for: $_GET[search]"; else echo 'Games'; ?></h1>
  <hr>
  <?php include 'random_headers_2.php'; ?>
  <?php paginate($page,$offset); ?>
  <hr>
  <form action="/games" method="get">
    <label for="search">Search: </label>
    <input type="text" name="search" id="search"><br>
    <label for="search"><small>Type in the name of the game you want.</small></label>
    <input type="submit" value="Look for Game">
  </form>
  <hr>
  <div class="games-results">
  <?php
    foreach(json_decode($result,true) as $game) {
      if (array_key_exists('id',$game)) {
        $cover_html = "";
        if (array_key_exists('cover', $game) && array_key_exists('url',$game['cover'])) $cover_html = "<img src=\"".$game['cover']['url']."\" alt=\"".htmlspecialchars($game['name'])."\"><br>"; else $cover_html = '<span class="no-img"> </span>';
        echo "<a class=\"flex-link\" href=\"/game/$game[id]/\">$cover_html$game[name]</a> ";
      }
    }
  ?>
  </div>
  <?php paginate($page,$offset); ?>
  <hr>
  <p><small>Game data provided by <a href="//igdb.com">IGDB</a></small></p>
  <hr>
  <a href="/">Back to Home Page</a>
</body>
</html>