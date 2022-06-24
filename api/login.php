<?php

$req = $_SERVER['REQUEST_METHOD'];

require_once '../dbase.php';

if( $req === 'POST' ) {
  $d = file_get_contents('php://input');
  $d = base64_decode($d);
  $d = json_decode($d);

  [$user, $pass] = $d;

  $db = new Database();

  $r = $db->get_user_role($user, $pass);

  if( $r === false ) {
    http_response_code(403);
    exit;
  }

  if( $r['role'] === 1 )
    header('location: ../admin.html');

  if( $r['role'] === 2 )
    header('location: ../cashier.html');

  if( $r['role'] === 3 )
    header('location: ../reader.html');

  exit;
}

http_response_code(403);
