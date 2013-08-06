<?php
interface myInterface {
	public function myFuncInt();
}

class myClass implements myInterface {
	private $var;

	public function myFuncCls() {
		echo 'ABC';
		var_dump($this->var);
	}

	public function myFuncInt() {
		echo '123';
	}
}

$cls = new myClass;
$cls->myFuncCls();
$cls->myFuncInt();
?>