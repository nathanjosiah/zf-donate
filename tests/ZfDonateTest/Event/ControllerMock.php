<?php
namespace ZfDonateTest\Event;


class ControllerMock {
	private $model,$arguments;
	public function __construct($model) {
		$this->model = $model;
	}
	public function Redirect() {
		return $this;
	}
	public function toRoute() {
		$this->arguments = func_get_args();
		return $this->model;
	}

	public function getArguments() {
		return $this->arguments;
	}
}