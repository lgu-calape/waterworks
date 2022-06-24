<?php
class Database extends SQLite3 {

  public function __construct() {
    $dbf = '/home/pi/waterworks/waterworks.db';

    $this->open($dbf);
    $this->exec("PRAGMA busy_timeout=10000");
    $this->exec("PRAGMA journal_mode=WAL;");
  }

  public function get_brgy() {
    $q = $this->query("SELECT id, name FROM barangay");

    $a = [];

    while( $r = $q->fetchArray(SQLITE3_ASSOC) )
      array_push($a, $r);

    return $a;
  }

  public function get_user_id($user, $pass) {
    $p = $this->prepare("SELECT id FROM user WHERE user=:u AND pass=:p");
    $p->bindParam(":u", $user);
    $p->bindParam(":p", $pass);

    $x = $p->execute();

    return $x->fetchArray(SQLITE3_NUM)[0];
  }

  public function get_user_role($id) {
    $p = $this->prepare("SELECT role FROM user WHERE id=:id");
    $p->bindParam(":id", $id, SQLITE3_INTEGER);

    $x = $p->execute();

    return $x->fetchArray(SQLITE3_NUM)[0];
  }

  public function get_consumer_by_brgy($brgy_id) {
    $p = $this->prepare("SELECT id,name,meter_no FROM consumer WHERE barangay_id=:bid");
    $p->bindParam(":bid", $brgy_id);

    $x = $p->execute();

    $a = [];

    while( $r = $x->fetchArray(SQLITE3_ASSOC) )
      array_push($a, $r);

    return $a;
  }

  public function record_usage($cid,$rid,$amt) {
    $p = $this->prepare("INSERT INTO reading(consumer_id,reader_id,m3_used) VALUES(:cid,:rid,:amt)");
    $p->bindParam(":cid", $cid, SQLITE3_INTEGER);
    $p->bindParam(":rid", $rid, SQLITE3_INTEGER);
    $p->bindParam(":amt", $amt, SQLITE3_FLOAT);

    return $p->execute();
  }
}
