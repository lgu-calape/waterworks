<?php

$req = $_SERVER['REQUEST_METHOD'];

require_once '../dbase.php';

if ( $req === 'POST' ) {
  $d = file_get_contents('php://input');
  $d = base64_decode($d);
  $d = json_decode($d);

  [$user, $pass] = $d;

  $db = new Database();

  $uid = $db->get_user_id($user, $pass);

  if ( $uid === false )
    exit(http_response_code(403));

  $ts = time() * 1000;
  $sid = hash('sha256', $uid.$ts);

  $db->session_set($sid,$uid,$ts);

  setcookie('sid', $sid, time()+3600*4);

  $role = $db->get_user_role($uid);

  if ( $role === 1 )
    header('location: ../admin.html');

  if ( $role === 2 )
    header('location: ../cashier.html');

  if ( $role === 3 )
    header('location: ../reader.html');

  exit;
}

http_response_code(403);
