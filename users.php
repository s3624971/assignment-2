<!DOCTYPE html>
<html>
  <head>
    <title>Users</title>
    <link rel="stylesheet" href="/css/style.css" type="text/css">
    <link rel="shortcut icon" type="image/png" href="/img/favicon.png"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>
  <body>
    <h1>Users</h1>
    <hr>
    <?php include 'random_headers_2.php'; ?>
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
    <p><a href="/">Back to Home Page</a></p>
  </body>
</html>