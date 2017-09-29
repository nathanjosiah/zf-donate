<?php

namespace ZfDonate\Payment\Gateway\Forte\Message;

use Omnipay\Common\Message\ResponseInterface;
use Guzzle\Http\Message\Response;
use Guzzle\Http\Message\Request;
use ZfDonate\Payment\Gateway\Forte\AvsResult;

class PurchaseResponse implements ResponseInterface {
	protected $request,$response,$fields;

	public function __construct(Request $request,Response $response) {
		$this->request = $request;
		$this->response = $response;
		$this->fields = $this->getFields($response->getBody(true));
	}

	public function getMessage() {
		return $this->fields['pg_response_description'];
	}

	public function isSuccessful() {
		return ($this->fields['pg_response_type'] === 'A');
	}

	public function getData() {
		return $this->response->getBody(true);
	}

	public function isRedirect() {
		return false;
	}

	public function getTransactionReference() {
		return $this->fields['pg_trace_number'];
	}

	public function getCode() {
		return $this->fields['pg_response_code'];
	}

	public function getRequest() {
		return $this->request;
	}

	public function getAvsResult() {
		return (isset($this->fields['pg_avs_result']) ? new AvsResult($this->fields['pg_avs_result']) : null);
	}

	public function getFields($responseBody) {
		$fields = [];
		$lines = explode("\n",$responseBody);
		foreach($lines as $line) {
			if($line === 'endofdata') break;
			$pieces = explode('=',$line);
			$fields[$pieces[0]] = $pieces[1];
		}
		return $fields;
	}

	public function isCancelled() {
		return false;
	}
}
