<?php
require_once '../dbase.php';

$req = $_SERVER['REQUEST_METHOD'];

if ( !isset($_COOKIE['sid']) )
  exit(http_response_code(403));

$db = new Database();

function is_base64($s) {
  return (bool) preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s);
}

if ( $req === "POST" ) {
  $d = file_get_contents("php://input");

  if ( !is_base64($d) )
    exit(http_response_code(403));

  $d = base64_decode($d);
  $d = json_decode($d);

  if ( count($d) !== 5 )
    exit(http_response_code(400));

  [$name,$phone,$addr,$brgy_id,$meter] = $d;

  if( ( $brgy_id < 1 ) && ( $brgy_id > 33 ) )
    exit(http_response_code(400));

  $db->new_consumer($name,$phone,$addr,$brgy_id,$meter);

  exit;
}

http_response_code(403);
