<?php
require_once '../dbase.php';

if( !isset($_COOKIE['sid']) )
  exit(http_response_code(403));

$db = new Database();

$sid = $_COOKIE['sid'];

if( !$db->session_get($sid) )
  exit(http_response_code(403));

$brgy_id = filter_input(INPUT_GET, 'id',FILTER_VALIDATE_INT);

echo json_encode($db->get_consumers_by_brgy($brgy_id));
