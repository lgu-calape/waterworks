<?php
require_once '../dbase.php';

if ( !isset($_COOKIE['sid']) )
  exit(http_response_code(403));

$db = new Database();

$sid = $_COOKIE['sid'];

if ( !$db->session_get($sid) ) exit(http_response_code(403));

if ( $cid = filter_input(INPUT_GET,'consumer_id', FILTER_VALIDATE_INT) ) {

  $aa = $db->get_readings_by_consumer_id($cid);

  if( !$aa ) exit(http_response_code(204));

  echo json_encode($aa);

  exit;
}

http_response_code(403);
