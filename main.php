<?php
  include 'datastore_setup';
  
  # Find User
  # -----------------------------------------------------------------------------
  $key = $datastore->key('user', $_SESSION['id']);
  $user = $datastore->lookup($key);
  #------------------------------------------------------------------------------
  
  # User log out when logout button pressed
  #------------------------------------------------------------------------------
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
    <link rel="shortcut icon" type="image/png" href="/img/favicon.png"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
  </head>
  <body>
    <h1>Good Gaming</h1>
    <hr>
    <?php include 'random_headers_2.php'; ?>
    <h2>Landing Page</h2>
    <p>Current User: <?php if (!is_null($user)) echo "<a href=\"/user/$_SESSION[id]/\">$user[name]</a>"; ?>.</p>
    <p><a href="/name/">Change Name</a> <a href="/password/">Change Password</a> <a href="/profilepic/">Change Profile Picture</a></p>
    <p><a href="/games/">Games</a> <a href="/searcharcades/">Search for Gaming Arcades in Melbourne</a> <a href="/users/">Users</a></p>
    <form action="/" method="post" style="display:contents;">
      <input type="hidden" name="logout" id="logout" value="yes">
      <input type="submit" value="Log Out">
    </form>
  </body>
</html>