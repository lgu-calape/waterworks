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

  public function get_user_role($user, $pass) {
    $p = $this->prepare("SELECT role FROM user WHERE user=:u AND pass=:p");
    $p->bindParam(":u", $user);
    $p->bindParam(":p", $pass);

    $x = $p->execute();;

    return $x->fetchArray(SQLITE3_ASSOC);
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
}
