<!DOCTYPE html>
<html>
  <head>
    <title>Users</title>
    <link rel="stylesheet" href="/css/style.css" type="text/css">
  </head>
  <body>
    <h1>Users</h1>
    <hr>
    <?php $headers_json = json_decode($header_imgs,true);
    foreach ($headers_json as $key => $value) {
      if ($key = 'cover') echo "<img src='".$value['cover']['url']."'>";
    } ?>
    <hr>
    <p>
      <?php
        $query = $datastore->query();
        $query->kind('user');
        
        $users = $datastore->runQuery($query);
        
        foreach ($users as $u) {
          echo '<a href="/user/'.$u['id']->pathEndIdentifier()."/\">$u[name]</a> ";
        }
      ?>
    </p>
    <p><a href="/">Back</a></p>
  </body>
</html>