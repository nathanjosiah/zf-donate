<?php

namespace ZfDonate\Payment;

class PaymentResultEntity {
	public $code,$transactionId,$message,$avsResult,$errors = [];
}

