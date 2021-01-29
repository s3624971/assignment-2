<?php
  # Activate session 
  #------------------------------------------------------------------------------
  session_start();
  #------------------------------------------------------------------------------
  
  # Include site setup
  #------------------------------------------------------------------------------
  include 'setup.php';
  include 'random_headers_1.php';
  $game_age_ratings = array("", "Three", "Seven", "Twelve", "Sixteen", "Eighteen", "RP", "EC", "E", "E10", "T", "M", "AO");
  #------------------------------------------------------------------------------
  
  $obj;
  $obj_fail;
  $obj_page_url;
  $make_comment;
  $page;
  function setup_object_page($object_name) {
    global $obj;
    global $obj_fail;
    global $obj_page_url;
    global $make_comment;
    global $page;
    
    $request_uri_segments = explode('/', $_SERVER['REQUEST_URI']);
    $page = "/$object_name.php";
    $make_comment = false;
    if (count($request_uri_segments) == 3 && $request_uri_segments[2] == '') {
      header('Location: /'.$object_name.'s/');
    }
    else if (count($request_uri_segments) == 3 && $request_uri_segments[1] == $object_name) { 
      header('Location: '.$_SERVER['REQUEST_URI'].'/'); 
      die();
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && count($request_uri_segments) > 3 && $request_uri_segments[3] == 'comment') {
      $make_comment = true;
      $obj_page_url = "";
      for ($i=0;$i<3;$i++) {
        $obj_page_url .= $request_uri_segments[$i].'/';
      }
    }
    $obj_fail = false;
    $obj = $request_uri_segments[2];
    if (!$obj) {
      $obj_fail = true;
      $make_comment = false;
    }
  }
  
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
      case '/user':
        header('Location: /users/');
        die();
      case '/users':
      case '/users/':
        $page = "/users.php";
        break;
      default:
        $page = "/main.php";
        break;
    }
    if (strpos($_SERVER['REQUEST_URI'], '/user/') !== false && strpos($_SERVER['REQUEST_URI'], '/user/') == 0) {
      setup_object_page('user');
    }
    else if (strpos($_SERVER['REQUEST_URI'], '/game/') !== false && strpos($_SERVER['REQUEST_URI'], '/game/') == 0) {
      setup_object_page('game');
    }
    else if (strpos($_SERVER['REQUEST_URI'], '/games') !== false && strpos($_SERVER['REQUEST_URI'], '/games') == 0) {
      $page = '/games.php';
    }
  }
  #if ($_SERVER['REQUEST_URI'] == '/igdb-test/' || $_SERVER['REQUEST_URI'] == '/igdb-test') $page = "/igdb-test-run.php";
  #------------------------------------------------------------------------------
  
  # Show the right page
  #------------------------------------------------------------------------------
  include $_SERVER['DOCUMENT_ROOT'].$page;
  #------------------------------------------------------------------------------
?>