<?php
  # Activate session 
  #------------------------------------------------------------------------------
  session_start();
  #------------------------------------------------------------------------------
  
  # Include site setup
  #------------------------------------------------------------------------------
  include 'setup.php';
  include 'random_headers_1.php';
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
    if (strpos($_SERVER['REQUEST_URI'], '/user/') !== false && strpos($_SERVER['REQUEST_URI'], '/user/') == 0) {
      $request_uri_segments = explode('/', $_SERVER['REQUEST_URI']);
      $page = "/user.php";
      $make_comment = false;
      if ($_SERVER['REQUEST_METHOD'] == 'POST' && count($request_uri_segments) > 3 && $request_uri_segments[3] == 'comment') {
        $make_comment = true;
        $user_page_url = "";
        for ($i=0;$i<3;$i++) {
          $user_page_url .= $request_uri_segments[$i].'/';
        }
      }
      $user_fail = false;
      $user = $request_uri_segments[2];
      if (!$user) {
        $user_fail = true;
        $make_comment = false;
      }
    }
  }
  if ($_SERVER['REQUEST_URI'] == '/igdb-test/' || $_SERVER['REQUEST_URI'] == '/igdb-test') $page = "/igdb-test-run.php";
  #------------------------------------------------------------------------------
  
  # Show the right page
  #------------------------------------------------------------------------------
  include $_SERVER['DOCUMENT_ROOT'].$page;
  #------------------------------------------------------------------------------
?>