<?php
  # Activate session 
  #------------------------------------------------------------------------------
  session_start();
  #------------------------------------------------------------------------------
  
  # Include site setup
  #------------------------------------------------------------------------------
  include 'setup.php';
  #------------------------------------------------------------------------------
  
  # Pick page to show based on url path, default to login page
  #------------------------------------------------------------------------------
  $page = "/login.php";
  if (isset($_SESSION['id']) && !is_null($_SESSION['id'])) {
    switch ($_SERVER['REQUEST_URI']) {
      case "/name":
      case "/name/":
        $page = "/name.php";
        break;
      case "/password":
      case "/password/":
        $page = "/password.php";
        break;
	  case "/profilepic":
      case "/profilepic/":
        $page = "/profilepic.php";
        break;
	  case "/searcharcades":
      case "/searcharcades/":
        $page = "/searcharcades.php";
        break;
      default:
        $page = "/main.php";
        break;
    }
  }
  if ($_SERVER['REQUEST_URI'] == '/igdb-test/' || $_SERVER['REQUEST_URI'] == '/igdb-test') $page = "/igdb-test-run.php";
  #------------------------------------------------------------------------------
  
  # Show the right page
  #------------------------------------------------------------------------------
  include $_SERVER['DOCUMENT_ROOT'].$page;
  #------------------------------------------------------------------------------
?>