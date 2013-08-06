<?php
class mySingleton {
	private static $myInstance;
	private $myVar;
	private function mySingleton() {
		$this->myVar = 1;
	}
	public static function getInstance() {
		if(self::$myInstance==null){
			self::$myInstance = new mySingleton;
		}
		return self::$myInstance;
	}
	public function watch() {
		var_dump($this->myVar);
	}
	public function change() {
		$this->myVar++;
	}
	public function get($key) {
		return $this->{$key};
	}
	public function set($key, $value) {
		$this->{$key} = $value;
	}
}

$cls = mySingleton::getInstance();
$cls->watch();
$cls2 = mySingleton::getInstance();
$cls->change();
$cls->watch();
$cls2->watch();
$cls->set('nom', 'toto');
echo $cls2->get('nom');

echo '<p>---</p>';

class mySingleton2 {
	private static $myInstance;
	private function mySingleton2() {
	}
	private static function getInstance() {
		if(self::$myInstance==null){
			self::$myInstance = new mySingleton2;
		}
		return self::$myInstance;
	}
	public static function get($key) {
		$inst = self::getInstance();
		return $inst->{$key};
	}
	public static function set($key, $value) {
		$inst = self::getInstance();
		$inst->{$key} = $value;
	}
}

mySingleton2::set('abc', 123);
echo mySingleton2::get('abc');
?>