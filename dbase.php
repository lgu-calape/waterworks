<?php
class Database extends SQLite3 {

  public function __construct() {
    $dbf = '/home/pi/waterworks/waterworks.db';

    $this->open($dbf);
    $this->exec("PRAGMA busy_timeout=10000");
    $this->exec("PRAGMA journal_mode=WAL;");
  }

  public function session_set($sid,$uid,$ts) {
    $p = $this->prepare("INSERT INTO sessions(id,user_id,created_at) VALUES(:sid,:uid,:oras)");
    $p->bindParam(":sid", $sid, SQLITE3_TEXT);
    $p->bindParam(":uid", $uid, SQLITE3_INTEGER);
    $p->bindParam(":oras", $ts);

    return $p->execute()->finalize();
  }

  public function session_get($sid) {
    $p = $this->prepare("SELECT user_id FROM sessions WHERE id=:sid");
    $p->bindParam(":sid", $sid, SQLITE3_TEXT);

    return $p->execute()->fetchArray(SQLITE3_NUM)[0];
  }

  public function get_brgy() {
    $q = $this->query("SELECT id, name FROM barangays");

    $a = [];

    while( $r = $q->fetchArray(SQLITE3_ASSOC) )
      array_push($a, $r);

    return $a;
  }

  public function get_user_id($user, $pass) {
    $p = $this->prepare("SELECT id FROM users WHERE user=:u AND pass=:p");
    $p->bindParam(":u", $user);
    $p->bindParam(":p", $pass);

    $x = $p->execute();

    return $x->fetchArray(SQLITE3_NUM)[0];
  }

  public function get_user_role($id) {
    $p = $this->prepare("SELECT role FROM users WHERE id=:id");
    $p->bindParam(":id", $id, SQLITE3_INTEGER);

    $x = $p->execute();

    return $x->fetchArray(SQLITE3_NUM)[0];
  }

  public function get_consumer_info($id) {
    $p = $this->prepare("SELECT a.name,a.bod,a.phone,a.address,a.meter_id,b.serial_no meter_serial_no,c.name brgy FROM consumers a JOIN meters b ON a.meter_id=b.id JOIN barangays c ON a.barangay_id=c.id WHERE a.id=:id");
    $p->bindParam(":id", $id, SQLITE3_INTEGER);

    return $p->execute()->fetchArray(SQLITE3_ASSOC);
  }

  public function get_consumers_by_brgy($brgy_id) {
    $p = $this->prepare("SELECT a.id,a.name,b.serial_no meter_no FROM consumers a JOIN meters b ON a.meter_id=b.id WHERE a.barangay_id=:bid ORDER BY a.name ASC");
    $p->bindParam(":bid", $brgy_id);

    $x = $p->execute();

    $a = [];

    while( $r = $x->fetchArray(SQLITE3_ASSOC) )
      array_push($a, $r);

    return $a;
  }

  public function get_consumers_nometer() {
    $p = $this->prepare("SELECT a.id,a.name,b.id AS brgy_id,b.name AS brgy_name FROM consumers a JOIN barangays b ON a.barangay_id=b.id WHERE a.meter_id IS NULL ORDER BY a.name ASC");
    $x = $p->execute();

    $a = [];

    while( $r = $x->fetchArray(SQLITE3_ASSOC) )
      array_push($a, $r);

    return $a;
  }

  public function get_consumer_meter_id($id) {
    $p = $this->prepare("SELECT meter_id FROM consumers WHERE id=:id");
    $p->bindParam(":id", $id, SQLITE3_INTEGER);

    return $p->execute()->fetchArray(SQLITE3_NUM)[0];
  }

  public function record_usage($mid,$rid,$amt) {
    $ts = time() * 1000;
    $p = $this->prepare("INSERT INTO readings(meter_id,reader_id,meter,created_at) VALUES(:mid,:rid,:amt,:dts)");
    $p->bindParam(":mid", $mid, SQLITE3_INTEGER);
    $p->bindParam(":rid", $rid, SQLITE3_INTEGER);
    $p->bindParam(":amt", $amt, SQLITE3_FLOAT);
    $p->bindParam(":dts", $ts);

    return $p->execute()->finalize();
  }

  public function get_readings_by_consumer_id($id) {
    $p = $this->prepare("SELECT t1.meter,IFNULL((t1.meter - LAG(t1.meter,1) OVER (ORDER BY t1.created_at)),t1.meter) m3,t1.created_at,t2.name AS reader FROM readings t1 JOIN users t2 ON t1.reader_id=t2.id JOIN consumers t3 ON t1.meter_id=t3.meter_id WHERE t3.id=:id ORDER BY t1.created_at DESC");
    $p->bindParam(":id", $id, SQLITE3_INTEGER);
    $a = [];

    $x = $p->execute();

    while( $r = $x->fetchArray(SQLITE3_ASSOC) )
      array_push($a, $r);

    return $a;
  }

  public function get_meter_readings($id) {
    $p = $this->prepare("SELECT t1.meter,IFNULL((t1.meter - LAG(t1.meter,1) OVER (ORDER BY t1.created_at)),t1.meter) m3,t1.created_at,t2.name AS reader_name FROM readings t1 JOIN users t2 ON t1.reader_id=t2.id WHERE t1.id=:id ORDER BY t1.created_at DESC");
    $p->bindParam(":id", $id, SQLITE3_INTEGER);
    $a = [];

    $x = $p->execute();

    while( $r = $x->fetchArray(SQLITE3_ASSOC) )
      array_push($a, $r);

    return $a;
  }

  public function new_meter($brand,$serial,$note) {
    $brand = strtoupper($brand);
    $serial = strtoupper($serial);

    $p = $this->prepare("INSERT INTO meters(brand,serial_no,note) VALUES(:f1,:f2,:f3)");
    $p->bindParam(":f1", $brand);
    $p->bindParam(":f2", $serial);
    $p->bindParam(":f3", $note);
    $p->execute();

    return $this->lastInsertRowID();
  }

  public function new_consumer($name,$phone,$addr,$brgy,$meter) {
    $name = strtoupper($name);
    $addr = strtoupper($addr);

    $p = $this->prepare("INSERT INTO consumers(name,phone,address,barangay_id,meter_id) VALUES(:f1,:f2,:f3,:f4,:f5)");
    $p->bindParam(":f1", $name);
    $p->bindParam(":f2", $phone);
    $p->bindParam(":f3", $addr);
    $p->bindParam(":f4", $brgy);
    $p->bindParam(":f5", $meter);
    $p->execute();

    return $this->lastInsertRowID();
  }
}
