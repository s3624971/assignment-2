<?php
  if (!$obj_fail) {
    $key = $datastore->key('user', $obj);
    $user_find = $datastore->lookup($key);
    if (is_null($user_find)) {
      $obj_fail = true;
    } else {
      if ($make_comment) {
        $new_comment_key = $datastore->key('user_comment');
        $new_comment_key_final = $datastore->allocateId($new_comment_key);
        $new_comment = $datastore->entity($new_comment_key_final, [
          'id' => $new_comment_key,
          'text' => htmlentities($_POST['comment']),
          'commenter_id' => $_SESSION['id'],
          'commentee_id' => $user_find['id']->pathEndIdentifier()
        ]);
        $datastore->insert($new_comment);
        header("Location: $obj_page_url");
        die();
      }
    }
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <title><?php if (!$obj_fail) { echo $user_find['name']; } else { echo "USER NOT FOUND"; } ?></title>
    <link rel="stylesheet" href="/css/style.css" type="text/css">
  </head>
  <body>
    <h1><?php if (!$obj_fail) { echo $user_find['name']; } else { echo "USER NOT FOUND"; } ?></h1>
    <hr>
    <?php $headers_json = json_decode($header_imgs,true);
    foreach ($headers_json as $key => $value) {
      if ($key = 'cover') echo "<img src='".$value['cover']['url']."'>";
    } ?>
    <hr>
  <?php if (!$obj_fail) { ?>
    <h2>User Info</h2>
    <p><img class='userpic' src="https://storage.googleapis.com/36249713375912-userpics/<?php echo $user_find['id']->pathEndIdentifier(); ?>.jpg"></p>
    <p>User ID: <?php echo $user_find['id']->pathEndIdentifier(); ?></p>
    <hr>
    <h2>Comments</h2>
    <?php
      $query = $datastore->query();
      $query->kind('user_comment');
      $query->filter('commentee_id','=',$user_find['id']->pathEndIdentifier());
      $query->order('id');
      
      $comments = $datastore->runQuery($query);
      
      foreach ($comments as $c) {
        $commenter_key = $datastore->key('user', $c['commenter_id']);
        $commenter = $datastore->lookup($commenter_key);
        
        echo "<div class='comment'>\n";
        if (!is_null($commenter)) {
          echo 'Comment by: <a href=\'/user/'.$commenter['id']->pathEndIdentifier()."/'>$commenter[name]</a>\n";
        }
        echo "<pre>$c[text]</pre>\n";
        echo "</div>\n";
      }
    ?>
    <hr>
    <h3>Add Comment</h3>
    <form action="comment/" method="post" style="max-width:226px;">
      <label for="comment"><small>Add a comment to <?php echo $user_find['name']; ?>'s page.</small></label><br>
      <textarea name="comment" id="comment"></textarea>
      <input type="submit" value="Post">
    </form>
    <hr>
  <?php } else { ?>
    <p>Use the link below to go back to the main page.</p>
  <?php } ?>
    <p><a href="/">Back</a></p>
  </body>
</html>