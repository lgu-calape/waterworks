<?php
require_once '../dbase.php';

$db = new Database();

$brgy_id = filter_input(INPUT_GET, 'id');

echo json_encode($db->get_consumer_by_brgy($brgy_id));
