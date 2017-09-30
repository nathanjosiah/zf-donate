<?php
namespace ZfDonateTest\Controller\TestAsset;

class ControllerStub {
	private $constructorArgs;
	public function __construct() {
		$this->constructorArgs = func_get_args();
	}
	public function getConstructorArgs() : array {
		return $this->constructorArgs;
	}
}