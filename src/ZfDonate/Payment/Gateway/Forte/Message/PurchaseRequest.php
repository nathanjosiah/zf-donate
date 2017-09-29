<?php

namespace ZfDonate\Payment\Gateway\Forte\Message;

class PurchaseRequest extends AbstractRequest {
	const RECUR_WEEKLY = 		10; // weekly every seven days
	const RECUR_BIWEEKLY = 	   	15; // biweekly every fourteen days
	const RECUR_MONTHLY = 		20; // monthly same day every month
	const RECUR_BIMONTHLY = 	25; // bi-monthly every two months
	const RECUR_QUARTERLY = 	30; // quarterly every 3 months
	const RECUR_SEMIANUALLY = 	35; // semiannually twice a year
	const RECUR_YEARLY = 		40; // yearly once year

	protected $brands = [
		\Omnipay\Common\CreditCard::BRAND_VISA => 'visa',
		\Omnipay\Common\CreditCard::BRAND_MASTERCARD => 'MAST',
		\Omnipay\Common\CreditCard::BRAND_AMEX => 'AMER',
		\Omnipay\Common\CreditCard::BRAND_DISCOVER => 'DISC',
		\Omnipay\Common\CreditCard::BRAND_DINERS_CLUB => 'DINE',
		\Omnipay\Common\CreditCard::BRAND_JCB => 'JCB',
	];

	public function getData() {
		$data = $this->getBaseData();
		/* @var $card \Omnipay\Common\CreditCard */
		if($card = $this->getCard()) {
			$data = array_merge($data,[
				'pg_transaction_type' => '10',
				'pg_total_amount' => $this->getAmount(),
				'Ecom_BillTo_Postal_Name_First' => $card->getFirstName(),
				'Ecom_BillTo_Postal_Name_Last' => $card->getLastName(),
				'Ecom_BillTo_Postal_Street_Line1' => $card->getBillingAddress1(),
				'Ecom_BillTo_Postal_City' => $card->getBillingCity(),
				'Ecom_BillTo_Postal_StateProv' => $card->getBillingState(),
				'Ecom_BillTo_Postal_PostalCode' => $card->getBillingPostcode(),
				'Ecom_Payment_Card_Name' => $card->getName(),
				'Ecom_Payment_Card_Type' => @$this->brands[$card->getBrand()],
				'Ecom_Payment_Card_Number' => $card->getNumber(),
				'Ecom_Payment_Card_ExpDate_Month' => str_pad($card->getExpiryMonth(),2,'0',STR_PAD_LEFT),
				'Ecom_Payment_Card_ExpDate_Year' => $card->getExpiryYear(),
				'ecom_consumerorderid' => $this->getOrderId(),
				// Any AVS matching requires extra fields so be careful when changing this.
				'pg_avs_method' => '00000'
			]);
		}
		elseif($bank_account = $this->getBankAccount()) {
			$data = array_merge($data,[
				'pg_transaction_type' => '20',
				'pg_avs_method' => '10000',
				'pg_total_amount' => $this->getAmount(),
				'Ecom_BillTo_Postal_Name_First' => $bank_account->getFirstName(),
				'Ecom_BillTo_Postal_Name_Last' => $bank_account->getLastName(),
				'Ecom_BillTo_Postal_Street_Line1' => $bank_account->getBillingAddress1(),
				'Ecom_BillTo_Postal_City' => $bank_account->getBillingCity(),
				'Ecom_BillTo_Postal_StateProv' => $bank_account->getBillingState(),
				'Ecom_BillTo_Postal_PostalCode' => $bank_account->getBillingPostcode(),
				'Ecom_Payment_Check_Account_Type' => $bank_account->getBankAccountType(),
				'Ecom_Payment_Check_Account' => $bank_account->getAccountNumber(),
				'Ecom_Payment_Check_TRN' => $bank_account->getRoutingNumber(),
				'ecom_consumerorderid' => $this->getOrderId(),
				'pg_entry_class_code' => 'WEB'
			]);
		}

		if($frequency = $this->getRecurrenceFrequency()) {
			$data['pg_schedule_frequency'] = $frequency;
		}
		if(($qty = $this->getRecurrenceQuantity()) !== null) {
			$data['pg_schedule_quantity'] = $qty;
		}
		if($amount = $this->getRecurrenceAmount()) {
			$data['pg_schedule_recurring_amount'] = $amount;
		}
		if($start_date = $this->getRecurrenceStartDate()) {
			$data['pg_schedule_start_date'] = $start_date->format('n/j/Y');
		}
		return $data;
	}

	public function getBankAccount() {
		return $this->getParameter('bankAccount');
	}
	public function setBankAccount($value) {
		return $this->setParameter('bankAccount',$value);
	}
	public function getRecurrenceFrequency() {
		return $this->getParameter('recurrenceFrequency');
	}
	public function setRecurrenceFrequency($value) {
		return $this->setParameter('recurrenceFrequency',$value);
	}
	public function getRecurrenceQuantity() {
		return $this->getParameter('recurrenceQuantity');
	}
	public function setRecurrenceQuantity($value) {
		return $this->setParameter('recurrenceQuantity',$value);
	}
	public function getRecurrenceAmount() {
		return $this->getParameter('recurrenceAmount');
	}
	public function setRecurrenceAmount($value) {
		return $this->setParameter('recurrenceAmount',$value);
	}
	public function getRecurrenceStartDate() {
		return $this->getParameter('recurrenceStartDate');
	}
	public function setRecurrenceStartDate(\DateTime $value) {
		return $this->setParameter('recurrenceStartDate',$value);
	}
	public function getOrderId() {
		return $this->getParameter('orderId');
	}
	public function setOrderId($value) {
		return $this->setParameter('orderId',$value);
	}
}
