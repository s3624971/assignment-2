<?php
  # ACQUIRE GAME INFORMATION
  # -------------------------------------------------------------------------------------------
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
  if (is_null($game) || !array_key_exists('name',$game)) { $obj_fail = true; }
  # -------------------------------------------------------------------------------------------
  
  # ADD COMMENT IF ONE IS POSTED
  # -------------------------------------------------------------------------------------------
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
  # -------------------------------------------------------------------------------------------
  
  # IF RATING IS SUBMITTED TRY TO SEND RATING TO DATASTORE
  # -------------------------------------------------------------------------------------------
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['stars'])) {
    $new_rating_key = $datastore->key('game_rating', $_SESSION['id'].'||||'.$obj);
    $old_rating = $datastore->lookup($new_rating_key);
    $new_rating = $datastore->entity($new_rating_key, [
      'game_id' => $obj,
      'user_id' => $_SESSION['id'],
      'rating' => $_POST['stars']
    ]);
    if (is_null($old_rating)) {
      $datastore->insert($new_rating);
    }
    else {
      $datastore->update($new_rating,array('allowOverwrite' => true));
    }
    header("Location: .");
    die();
  }
  # -------------------------------------------------------------------------------------------
  
  # CALCULATE AVERAGE RATING
  # -------------------------------------------------------------------------------------------
  $avgnum = 0;
  $count = 0;
  $query = $datastore->query();
  $query->kind('game_rating');
  $query->filter('game_id', '=', $obj);
  $results = $datastore->runQuery($query);
  foreach ($results as $user) {
      $avgnum += $user['rating'];
      $count++;
  }
  if ($count > 0) $avgnum /= $count;
  # -------------------------------------------------------------------------------------------
  
  # FUNCTION TO DISPLAY RATING
  # -------------------------------------------------------------------------------------------
  function show_stars($rating) {
    $stars_result = "";
    for ($i=1;$i<=5;$i++) {
      $color='grey';
      $alt = "";
      if ($i <= $rating) $color = 'red';
      if ($i == $rating) $alt = "$i stars out of 5";
      $stars_result .= "<img src=\"/img/star-$color.png\" alt=\"$alt\" width=14 height=14>";
    }
    return $stars_result;
  }
  # -------------------------------------------------------------------------------------------
?>
<!DOCTYPE html>
<html>
  <head>
    <title><?php if (!$obj_fail) { echo $game['name']; } else { echo "GAME NOT FOUND"; } ?></title>
    <link rel="stylesheet" href="/css/style.css" type="text/css">
    <link rel="shortcut icon" type="image/png" href="/img/favicon.png"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>
  <body>
    <h1><?php if (!$obj_fail) { echo $game['name']; } else { echo "GAME NOT FOUND"; } ?></h1>
    <?php
      # DISPLAY ALTERNATIVE NAMES
      if (!$obj_fail && array_key_exists('alternative_names',$game)) {
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
    <?php if (!$obj_fail) { ?>
    <h2>Game Info</h2>
    <p><?php if (array_key_exists('cover',$game) && array_key_exists('url',$game['cover'])) { echo '<img src=\''.$game['cover']['url'].'\' alt=\''.htmlspecialchars($game['name']).'\'>'; } ?></p>
    <?php 
      # Function for showing a large block of text
      # -------------------------------------------------------------------------------------------
      function display_large_text($key,$array,$title) {
        if (array_key_exists($key,$array)) {
          echo "<h3>$title</h3>";
          echo "<pre class='text'>$array[$key]</pre>";
        }
      }
      # -------------------------------------------------------------------------------------------
      
      # Function for showing a sequence of small texts
      # -------------------------------------------------------------------------------------------
      function display_list_small_texts($key,$array,$title_str,$sub_key = 'name') {
        if (array_key_exists($key,$array)) {
          if (count($array[$key]) > 1) $title_str .= 's';
          echo "<h3>$title_str</h3>\n    <p>";
          $comma = '';
          foreach ($array[$key] as $item) {
            if (array_key_exists($sub_key,$item)) echo "$comma$item[$sub_key]";
            $comma = ', ';
          }
          echo ".</p>\n";
        }
      }
      # -------------------------------------------------------------------------------------------
      
      # DISPLAY SUMMARY
      display_large_text('summary',$game,'Summary');
      # DISPLAY STORYLINE
      display_large_text('storyline',$game,'Storyline');
      # DISPLAY GENRES
      display_list_small_texts('genres',$game,'Genre');
      # DISPLAY GAME MODES
      display_list_small_texts('game_modes',$game,'Game Mode');
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
          display_list_small_texts('content_descriptions',$rating,'Content Description','description');
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
        foreach ($game['websites'] as $website) {
          if (array_key_exists('url',$website)) echo "    <p><a href=\"$website[url]\">$website[url]</a></p>\n";
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
          echo '      <img src=\''.$art['url'].'\' alt=\'\'>'."\n";
        }
        echo "    </div>\n";
      }
    ?>
    <hr>
    <h2>Ratings</h2>
    <?php 
      $query = $datastore->query();
      $query->kind('game_rating');
      $query->filter('game_id','=',$obj);
      
      $ratings = $datastore->runQuery($query);
      
      try {
        if (!is_null($ratings->current())) {
          echo "<span class=\"rating-box\">
                  <span class=\"rating-box-main\">
                    <span>Average Rating</span>
                    <span>: </span>
                    <span>".show_stars(round($avgnum)).'</span>
                  </span>
                  <div style="clear:both;"></div>
                </span><br>';
          
          foreach ($ratings as $r) {
            $user_name = "";
            $user_key = $datastore->key('user', $r['user_id']);
            $user = $datastore->lookup($user_key);
            if (!is_null($user)) $user_name = $user['name'];
            echo "<span class=\"rating-box\">
                 ".'<span class="rating-box-picture"><a href=\'/user/'.$r['user_id']."/'><img src=\"https://storage.googleapis.com/thumbnail-assignment-2/".$r['user_id'].".jpg\" alt=\"".htmlspecialchars($user_name)."\"></a></span>
                    <span class=\"rating-box-main\">
                      <span>Rating by: <a href=\"/user/$r[user_id]/\">$user_name</a></span>
                      <span>: </span>
                      <span>".show_stars($r['rating']).'</span>
                    </span>
                    <div style="clear:both;"></div>
                  </span><br>';
          }
        }
      } catch (Exception $e) {
        echo $e->getMessage();
      }
    ?>
    <hr>
    <h3>Rate Game</h2>
    <form action="" method="post" class="game-rate" style="max-width:160px;">
      <label for="stars">Rating (1 to 5)</label><br>
      <span class="star-box">
        <input type="radio" class="star" id="star-1" name="stars" value="1">
        <label class="star-add" for="star-1">1</label>
        
        <input type="radio" class="star" id="star-2" name="stars" value="2">
        <label class="star-add" for="star-2">2</label>
        
        <input type="radio" class="star" id="star-3" name="stars" value="3">
        <label class="star-add" for="star-3">3</label>
        
        <input type="radio" class="star" id="star-4" name="stars" value="4">
        <label class="star-add" for="star-4">4</label>
        
        <input type="radio" class="star" id="star-5" name="stars" value="5" checked="checked">
        <label class="star-add" for="star-5">5</label>
      </span>
      <input style="display:block;margin-left:auto;" type="submit" value="Submit Rating">
    </form>
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
    <hr>
    <p><small>Game data provided by <a href="//igdb.com">IGDB</a></small></p>
    <hr>
    <?php } else { ?>
    <p>Use the link below to go back to the main page.</p>
    <?php } ?>
    <p><a href="/">Back to Home Page</a></p>
  </body>
</html>