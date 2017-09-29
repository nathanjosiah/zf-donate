<?php

namespace ZfDonate\Payment\Gateway\Forte;

class AvsResult {
	const RESULT_NOT_PERFORMED = 0;
	const RESULT_PASSED = 3;
	const RESULT_FAILED = 4;

	protected $result,$parsedResults;
	public function __construct($result) {
		$this->result = $result;
	}

	public function getParsedResults() {
		if(!isset($this->parsedResults)) {
			$results = [self::RESULT_NOT_PERFORMED,self::RESULT_NOT_PERFORMED,self::RESULT_NOT_PERFORMED,self::RESULT_NOT_PERFORMED,self::RESULT_NOT_PERFORMED];
			for($i=0;$i<strlen($this->result);$i++) {
				$results[$i] = $this->parseFlag(substr($this->result,$i,1));
			}
			$this->parsedResults = $results;
		}
		return $this->parsedResults;
	}

	protected function parseFlag($flag) {
		switch((int)$flag) {
			case 0: return self::RESULT_NOT_PERFORMED;
			case 3: return self::RESULT_PASSED;
			case 4: return self::RESULT_FAILED;
		}
	}

	public function getCreditCardZipcodeResult() {
		return $this->getParsedResults()[0];
	}
	public function getCreditCardStreetNumberResult() {
		return $this->getParsedResults()[1];
	}
	public function getStateZipcodeResult() {
		return $this->getParsedResults()[2];
	}
	public function getStateAreaCodeResult() {
		return $this->getParsedResults()[3];
	}
	public function getAnonymousEmailResult() {
		return $this->getParsedResults()[4];
	}

	public function __toString() {
		return implode('',$this->getParsedResults());
	}
}
