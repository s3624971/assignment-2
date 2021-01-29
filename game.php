<?php
  $client_id = 'gn969nmh00hd9nxtuawgxinki7rop7';
  $access_token = 'bu6p9wzid55nv8kslefy1d39akawil';
  $post_body = array(
    'endpoint'=>'games',
    'fields'=>'name, alternative_names.name, summary, storyline, cover.url, artworks.url, game_modes.name, game_engines.name, game_engines.description, game_engines.logo.url, age_ratings.category, age_ratings.rating, age_ratings.synopsis, age_ratings.content_descriptions.description, first_release_date, franchise.name, genres.name, websites.url',
    'limit'=>'1',
    'where'=>'id = '.$obj
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
  $game = json_decode($result,true)[0];
  if (is_null($game)) { $obj_fail = true; }
  else if ($make_comment) {
    $new_comment_key = $datastore->key('game_comment');
    $new_comment_key_final = $datastore->allocateId($new_comment_key);
    $new_comment = $datastore->entity($new_comment_key_final, [
      'id' => $new_comment_key,
      'text' => htmlentities($_POST['comment']),
      'commenter_id' => $_SESSION['id'],
      'game_id' => $obj
    ]);
    $datastore->insert($new_comment);
    header("Location: $obj_page_url");
    die();
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <title><?php if (!$obj_fail) { echo $game['name']; } else { echo "GAME NOT FOUND"; } ?></title>
    <link rel="stylesheet" href="/css/style.css" type="text/css">
  </head>
  <body>
    <h1><?php if (!$obj_fail) { echo $game['name']; } else { echo "GAME NOT FOUND"; } ?></h1>
    <?php
      # DISPLAY ALTERNATIVE NAMES
      if (array_key_exists('alternative_names',$game)) {
        echo '<p>(AKA: ';
        $comma = '';
        foreach ($game['alternative_names'] as $aka) {
          if (array_key_exists('name',$aka)) {
            echo "$comma$aka[name]";
            $comma = ', ';
          }
        }
        echo ')';
      }
    ?>
    <hr>
    <?php include 'random_headers_2.php'; ?>
    <h2>Game Info</h2>
    <p><?php if (array_key_exists('cover',$game) && array_key_exists('url',$game['cover'])) { echo '<img src=\''.$game['cover']['url'].'\'>'; } ?></p>
    <?php 
      # DISPLAY SUMMARY
      if (array_key_exists('summary',$game)) {
        echo '<h3>Summary</h3>';
        echo "<pre class='text'>$game[summary]</pre>";
      }
      # DISPLAY STORYLINE
      if (array_key_exists('storyline',$game)) {
        echo '<h3>Storyline</h3>';
        echo "<pre class='text'>$game[storyline]</pre>";
      }
      # DISPLAY GENRES
      if (array_key_exists('genres',$game)) {
        if (count($game['genres']) > 1) $gn_title = 'Genres'; else $gn_title = 'Genre';
        echo "<h3>$gn_title</h3>\n    <p>";
        $comma = '';
        foreach ($game['genres'] as $genre) {
          if (array_key_exists('name',$genre)) echo "$comma$genre[name]";
          $comma = ', ';
        }
        echo ".</p>\n";
      }
      # DISPLAY GAME MODES
      if (array_key_exists('game_modes',$game)) {
        if (count($game['game_modes']) > 1) $gm_title = 'Game Modes'; else $gm_title = 'Game Mode';
        echo "<h3>$gm_title</h3>\n    <p>";
        $comma = '';
        foreach ($game['game_modes'] as $mode) {
          if (array_key_exists('name',$mode)) echo "$comma$mode[name]";
          $comma = ', ';
        }
        echo ".</p>\n";
      } 
      # DISPLAY RELEASE DATE
      if (array_key_exists('first_release_date',$game)) {
        echo "    <h3>Release Date</h3>\n<p>    ".date('jS F, Y',$game['first_release_date'])."</p>\n";
      }
      # DISPLAY AGE RATINGS
      if (array_key_exists('age_ratings',$game)) {
        echo "    <h3>Age Ratings</h3>\n";
        foreach ($game['age_ratings'] as $rating) {
          switch ($rating['category']) {
            case 1:
              echo "<h4>PEGI</h4>\n";
              break;
            default:
              echo "<h4>ESRB</h4>\n";
              break;
          }
          if (array_key_exists('rating',$rating)) echo '<p>Rating: '.$game_age_ratings[$rating['rating']]."</p>\n";
          if (array_key_exists('content_descriptions',$rating)) {
            if (count($rating['content_descriptions']) > 1) $ct_title = 'Content Descriptions'; else $ct_title = 'Content Description';
            echo "<p class='text'>$ct_title: ";
            $comma = "";
            foreach ($rating['content_descriptions'] as $ct) {
              if (array_key_exists('description',$ct)) {
                echo "$comma$ct[description]";
                $comma = ', ';
              }
            }
            echo '.</p>';
          }
          if (array_key_exists('synopsis',$rating)) echo "<pre class='text'>Synopsis: $rating[synopsis]</pre>\n";
        }
        if (array_key_exists('rating_cover_url',$game['age_ratings'])) {
          echo "    <div class='border'>\n";
          foreach ($game['age_ratings'] as $rating) {
            echo '      <img src=\''.$rating['rating_cover_url'].'\'>'."\n";
          }
          echo "    </div>\n";
        }
      }
      # DISPLAY GAME ENGINES
      if (array_key_exists('game_engines',$game)) {
        if (count($game['game_engines']) > 1) $gm_title = 'Game Engines'; else $gm_title = 'Game Engine';
        echo "    <h3>$gm_title</h3>\n";
        $comma = '';
        foreach ($game['game_engines'] as $engine) {
          if (array_key_exists('name',$engine)) echo "    <h4>$engine[name]</h4>\n";
          if (array_key_exists('description',$engine)) echo "    <p>$engine[description]</p>\n";
          if (array_key_exists('logo',$engine) && array_key_exists('url',$engine['logo'])) { echo '<img src=\''.$engine['logo']['url'].'\'>'; }
        }
      } 
      # DISPLAY WEBSITES
      if (array_key_exists('websites',$game)) {
        if (count($game['websites']) > 1) $wb_title = 'Websites'; else $wb_title = 'Website';
        echo "    <h3>$wb_title</h3>\n";
        $comma = '';
        foreach ($game['websites'] as $website) {
          if (array_key_exists('url',$website)) echo "    <p><a href=\"$website[url]\">$comma$website[url]</a></p>\n";
        }
      } 
      # DISPLAY FRANCHISE
      if (array_key_exists('franchise',$game) && array_key_exists('name',$game['franchise'])) {
        echo "<h3>Franchise</h3>\n<p>".$game['franchise']['name'].".</p>\n";
      }
      # DISPLAY ARTWORKS
      if (array_key_exists('artworks',$game)) {
        echo "    <h3>Artworks</h3>\n";
        echo "    <div class='border'>\n";
        foreach ($game['artworks'] as $art) {
          echo '      <img src=\''.$art['url'].'\'>'."\n";
        }
        echo "    </div>\n";
      }
    ?>
    <hr>
    <h2>Comments</h2>
    <?php
      $query = $datastore->query();
      $query->kind('game_comment');
      $query->filter('game_id','=',$obj);
      
      $comments = $datastore->runQuery($query);
      
      try {
        if (!is_null($comments->current())) {
          foreach ($comments as $c) {
            include 'comment.php';
          }
        }
      } catch (Exception $e) {
        echo $e->getMessage();
      }
    ?>
    <hr>
    <h3>Add Comment</h3>
    <form action="comment/" method="post" style="max-width:226px;">
      <label for="comment"><small>Add a comment on <?php echo $game['name']; ?>.</small></label><br>
      <textarea name="comment" id="comment"></textarea>
      <input type="submit" value="Post">
    </form>
    <?php
     # echo "<div class='results-grid'>";
     # echo read_out_json_result(json_decode($result,true),2);
     # echo "</div>";
     # curl_close($curl);
      
      
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
    ?>
    
    <hr>
    <p><small>Game data provided by <a href="//igdb.com">IGDB</a></small></p>
    <hr>
    <p><a href="/">Back</a></p>
  </body>
</html>