<?php
class Database extends SQLite3 {

  public function __construct() {
    $dbf = '/home/pi/waterworks/waterworks.db';

    $this->open($dbf);
    $this->exec("PRAGMA busy_timeout=10000");
    $this->exec("PRAGMA journal_mode=WAL;");
  }

  public function session_set($sid,$uid,$ts) {
    $p = $this->prepare("INSERT INTO session(id,user_id,created_at) VALUES(:sid,:uid,:oras)");
    $p->bindParam(":sid", $sid, SQLITE3_TEXT);
    $p->bindParam(":uid", $uid, SQLITE3_INTEGER);
    $p->bindParam(":oras", $ts);

    return $p->execute()->finalize();
  }

  public function session_get($sid) {
    $p = $this->prepare("SELECT user_id FROM session WHERE id=:sid");
    $p->bindParam(":sid", $sid, SQLITE3_TEXT);

    return $p->execute()->fetchArray(SQLITE3_NUM)[0];
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
    $p = $this->prepare("SELECT id,name,meter_no FROM consumer WHERE barangay_id=:bid ORDER BY name ASC");
    $p->bindParam(":bid", $brgy_id);

    $x = $p->execute();

    $a = [];

    while( $r = $x->fetchArray(SQLITE3_ASSOC) )
      array_push($a, $r);

    return $a;
  }

  public function record_usage($cid,$rid,$amt) {
    $ts = time() * 1000;
    $p = $this->prepare("INSERT INTO reading(consumer_id,reader_id,m3_used,created_at) VALUES(:cid,:rid,:amt,:dts)");
    $p->bindParam(":cid", $cid, SQLITE3_INTEGER);
    $p->bindParam(":rid", $rid, SQLITE3_INTEGER);
    $p->bindParam(":amt", $amt, SQLITE3_FLOAT);
    $p->bindParam(":dts", $ts);

    return $p->execute()->finalize();
  }

  public function get_readings_by_consumer_id($id) {
    $p = $this->prepare("SELECT t1.m3_used,t1.created_at,t2.name AS reader_name FROM reading t1 JOIN user t2 ON t1.reader_id=t2.id WHERE t1.consumer_id=:id ORDER BY t1.created_at DESC");
    $p->bindParam(":id", $id, SQLITE3_INTEGER);
    $a = [];

    $x = $p->execute();

    while( $r = $x->fetchArray(SQLITE3_ASSOC) )
      array_push($a, $r);

    return $a;
  }
}
