<?php
require_once '../dbase.php';

if ( !isset($_COOKIE['sid']) )
  exit(http_response_code(403));

$sid = $_COOKIE['sid'];

$db = new Database();

if ( !$db->session_get($sid) )
  exit(http_response_code(403));

if ( $id = filter_input(INPUT_GET,'id', FILTER_VALIDATE_INT) ) {

  echo json_encode($db->get_consumer_info($id));
}


