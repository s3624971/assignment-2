<?php 
  $headers_json = json_decode($header_imgs,true);
  echo '<div class="header-imgs">'."\n";
  foreach ($headers_json as $header) {
    $link_to_game = array('','');
    if (isset($_SESSION['id']) && !is_null($_SESSION['id'])) $link_to_game = array("<a style=\"display:contents;\" href=\"/game/$header[id]/\">",'</a>');
    echo "$link_to_game[0]<img src='".$header['cover']['url']."'>$link_to_game[1]";
  } 
  echo "\n</div>";
?>
<hr>