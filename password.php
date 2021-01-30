<?php
  # Change password and then log out when old password is correct
  #------------------------------------------------------------------------------
  if (!empty($_POST)) {
    # Find and authenticate user for changing password
    $pass_key = $datastore->key('user', $_SESSION['id']);
    $pass_user = $datastore->lookup($pass_key);
    if (!is_null($pass_user) && $pass_user['password'] == $_POST['password']) { 
      # Change password before logging out
      $pass_user['password'] = $_POST['password-new'];
      $datastore->update($pass_user);
      # End session when logging out
      unset($_SESSION['id']);
      setcookie(session_name(), '', time() - 3600);
      session_destroy();
      # Redirect to login page
      header('Location: /');
      die();
    } else $pass_error = "User password is incorrect.";
  }
  #------------------------------------------------------------------------------
?>
<html>
  <head>
    <title>Change Password</title>
    <link rel="stylesheet" type="text/css" href="/css/style.css?v=1">
    <link rel="shortcut icon" type="image/png" href="/img/favicon.png"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
      form { max-width:260px; }
    </style>
  </head>
  <body>
    <h1>Change Password</h1>
    <hr>
    <?php include 'random_headers_2.php'; ?>
    <form action="/password/" method="post">
      <div><label for="password">Old Password: </label><input type="password" id="password" name="password"></div>
      <div><label for="password-new">New Password: </label><input type="password" id="password-new" name="password-new"></div>
      <div><input type="submit"></div>
    </form><?php if (isset($pass_error)) echo "\n<p>$pass_error</p>"; ?>
    <p><a href="/">Back to Home Page</a></p>
  </body>
</html>