<?php
  function log_in($id, $pw, $datastore) {
    # Find and authenticate user for logging in
    $login_key = $datastore->key('user', $id);
    $login_user = $datastore->lookup($login_key);
    if (!is_null($login_user) && $login_user['password'] == $pw) { 
      # Set user ID to session when logging in
      $_SESSION['id'] = $id;
      # Reload page with GET so the "back" button won't need to resend POST data
      header('Location: /');
      die();
    } else { return "User ID or password is invalid."; }
  }
  # Attempt to log in to user account when login form submitted
  #------------------------------------------------------------------------------
  $login_error = "";
  if (!empty($_POST)) {
    switch ($_SERVER['REQUEST_URI']) {
      case "/login/":
        if (str_replace(' ','',$_POST['id']) != '') {
          $login_error = log_in($_POST['id'], $_POST['password'], $datastore);
        } else { $login_error = "User ID or password is invalid."; }
        break;
      case "/signup/":
        if (str_replace(' ','',$_POST['s-id']) != '') {
          if (str_replace(' ','',htmlentities($_POST['s-name'])) != '') {
            if (str_replace(' ','',$_POST['s-password']) != '') {
              $new_user_key = $datastore->key('user', $_POST['s-id']);
              $datastore_user = $datastore->lookup($new_user_key);
              if (is_null($datastore_user)) {
                $new_user = $datastore->entity($new_user_key, [
                  'id' => $new_user_key,
                  'name' => htmlentities($_POST['s-name']),
                  'password' => $_POST['s-password']
                ]);
                $datastore->insert($new_user);
                $signup_error = log_in($_POST['s-id'], $_POST['s-password'], $datastore);
              } else $signup_error = "This User ID already exists. Try another one.";
            } else $signup_error = "Password cannot be empty.";
          } else $signup_error = "Name cannot be empty.";
        } else $signup_error = "User ID cannot be empty.";
        break;
    }
  }
  #------------------------------------------------------------------------------
  include 'random_headers_1.php';
?>
<html>
  <head>
    <title>Welcome to Good Gaming!</title>
    <link rel="stylesheet" type="text/css" href="/css/style.css?v=1">
  </head>
  <body>
    <h1>Good Gaming</h1>
    <hr>
    <?php $headers_json = json_decode($header_imgs,true);
    foreach ($headers_json as $key => $value) {
      if ($key = 'cover') echo "<img src='".$value['cover']['url']."'>";
    } ?>
    <hr>
    <h2>Log In</h2>
    <form action="/login/" method="post">
      <div><label for="id">User ID: </label><input type="text" id="id" name="id"></div>
      <div><label for="password">Password: </label></label><input type="password" id="password" name="password"></div>
      <div><input type="submit"></div>
    </form><?php if (isset($login_error)) echo "\n<p>$login_error</p>"; ?>
    <h2>New to Good Gaming? Make an Account!</h2>
    <form action="/signup/" method="post">
      <div><label for="s-id">User ID: </label><input type="text" id="s-id" name="s-id"><br><small><label for="s-id">A unique user identifier for the system.</label></small></div>
      <div><label for="s-name">Name: </label><input type="text" id="s-name" name="s-name"><br><small><label for="s-name">Your display name, seen by others.</label></small></div>
      <div><label for="s-password">Password: </label></label><input type="password" id="s-password" name="s-password"><br><small><label for="s-password">A secret string used to log in.</label></small></div>
      <div><input type="submit"></div>
    </form><?php if (isset($signup_error)) echo "\n<p>$signup_error</p>"; ?>
  </body>
</html>