<?php
require_once '../dbase.php';

$req = $_SERVER['REQUEST_METHOD'];

if ( !isset($_COOKIE['sid']) )
  exit(http_response_code(403));

$db = new Database();

$sid = $_COOKIE['sid'];

$rid = $db->session_get($sid);

if( !$rid ) exit(http_response_code(403));

if ( $req === 'POST' ) {
  $d = file_get_contents('php://input');
  $d = base64_decode($d);

  [$n1,$n2] = explode(',', $d);

  $cid = filter_var($n1, FILTER_VALIDATE_INT);
  $amt = filter_var($n2, FILTER_VALIDATE_FLOAT);

  $db->record_usage($cid,$rid, $amt);

  exit;
}

http_response_code(403);
