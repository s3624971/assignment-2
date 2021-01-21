<?php
  include 'datastore_setup';
  
  # User log out when logout button pressed
  #------------------------------------------------------------------------------
  $key = $datastore->key('user', $_SESSION['id']);
  $user = $datastore->lookup($key);
  
  if (!empty($_POST)) {
    if (isset($_POST['logout']) && !is_null($_POST['logout'])) {
      # End session when logging out
      unset($_SESSION['id']);
      setcookie(session_name(), '', time() - 3600);
      session_destroy();
      # Reload page with GET so the "back" button won't need to resend POST data
      header('Location: /');
      die();
    }
  }
  #------------------------------------------------------------------------------
?>
<html>
  <head>
    <title>Good Gaming</title>
    <link rel="stylesheet" type="text/css" href="/css/style.css?v=1">
  </head>
  <body>
    <h1>Good Gaming</h1>
    <hr>
    <h2>Landing Page</h2>
    <p>Current User: <?php if (!is_null($user)) echo $user['name']; ?>.</p>
    <p><a href="/name/">Change Name</a> <a href="/password/">Change Password</a></p>
    <form action="/" method="post" style="display:contents;">
      <input type="hidden" name="logout" id="logout" value="yes">
      <input type="submit" value="Log Out">
    </form>
  </body>
</html>