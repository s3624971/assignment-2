<?php
  # Change Username and then move back to home page if new name provided
  #------------------------------------------------------------------------------
  if (!empty($_POST)) {
    if (str_replace(' ','',$_POST['name']) != '') {
      # Find and authenticate user for changing name
      $rename_key = $datastore->key('user', $_SESSION['id']);
      $rename_user = $datastore->lookup($rename_key);
      $rename_user['name'] = htmlentities($_POST['name']);
      $datastore->update($rename_user);
      # Redirect to home page
      header('Location: /');
      die();
    } else { $rename_error = "User name cannot be empty."; }
  }
  #------------------------------------------------------------------------------
?>
<html>
  <head>
    <title>Change Name</title>
    <link rel="stylesheet" type="text/css" href="/css/style.css?v=1">
    <link rel="shortcut icon" type="image/png" href="/img/favicon.png"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
      form { max-width:234px; }
    </style>
  </head>
  <body>
    <h1>Change Name</h1>
    <hr>
    <?php include 'random_headers_2.php'; ?>
    <form action="/name/" method="post">
      <div><label for="name">New Name: </label><input type="text" id="name" name="name"></div>
      <div><input type="submit"></div>
    </form><?php if (isset($rename_error)) echo "\n<p>$rename_error</p>"; ?>
    <p><a href="/">Back to Home Page</a></p>
  </body>
</html>