<?php
require_once '../dbase.php';

if( !isset($_COOKIE['sid']) )
  exit(http_response_code(403));

$db = new Database();

$sid = $_COOKIE['sid'];

if( !$db->session_get($sid) )
  exit(http_response_code(403));

$brgy_id = filter_input(INPUT_GET, 'id',FILTER_VALIDATE_INT);

$data = $db->get_consumers_nometer();

if( !$data ) exit(http_response_code(204));

echo json_encode($data);
