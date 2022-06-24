<?php
require_once '../dbase.php';

$db = new Database();

echo json_encode($db->get_brgy());
