<?php

namespace ZfDonate\Form;

use Zend\Filter\PregReplace;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\CreditCard;
use Zend\Validator\GreaterThan;
use Zend\Validator\NotEmpty;

class DonateForm extends Form implements InputFilterProviderInterface {
	public function init() {
		$this->add([
			'name' => 'amount',
			'options' => [
				'label' => 'Amount',
			]
		]);
		$this->add([
			'name' => 'recurring',
			'type' => 'Checkbox',
			'options' => [
				'label' => 'I would like this to be a monthly contribution.',
			]
		]);
		$this->add([
			'name' => 'first_name',
			'options' => [
				'label' => 'First Name',
			]
		]);
		$this->add([
			'name' => 'last_name',
			'options' => [
				'label' => 'Last Name',
			]
		]);
		$this->add([
			'name' => 'email',
			'options' => [
				'label' => 'Email Address',
			]
		]);
		$this->add([
			'name' => 'cc_number',
			'options' => [
				'label' => 'Credit Card Number',
			]
		]);
		$this->add([
			'name' => 'cc_expiration_month',
			'type' => 'Select',
			'options' => [
				'label' => 'Expiration Month',
				'value_options' => [
					'' => 'Please Select',
					1 => '01 - January',
					2 => '02 - February',
					3 => '03 - March',
					4 => '04 - April',
					5 => '05 - May',
					6 => '06 - June',
					7 => '07 - July',
					8 => '08 - August',
					9 => '09 - September',
					10 => '10 - October',
					11 => '11 - November',
					12 => '12 - December',
				]
			]
		]);
		$this->add([
			'name' => 'cc_expiration_year',
			'type' => 'Select',
			'options' => [
				'label' => 'Expiration Year',
				'value_options' => ['' => 'Please Select'] + array_combine(range(idate('Y'),idate('Y')+15),range(idate('Y'),idate('Y')+15))
			]
		]);
		$this->add([
			'name' => 'ccv',
			'options' => [
				'label' => 'CCV',
			]
		]);
	}

	public function getInputFilterSpecification() {
		return [
			[
				'required' => true,
				'name' => 'amount',
				'filters' => [
					[
						'name' => PregReplace::class,
						'options' => [
							'pattern' => '/[^0-9\.]/',
							'replacement' => ''
						]
					],
					[
						'name' => \Zend\Filter\Callback::class,
						'options' => [
							'callback' => function($value) {
								return (float)$value;
							}
						]
					]
				],
				'validators' => [
					[
						'name' => 'NotEmpty',
						'options' => [
							'messages' => [
								NotEmpty::IS_EMPTY => 'Amount is required.'
							]
						]
					],
					[
						'name' => 'GreaterThan',
						'options' => [
							'min' => 0,
							'messages' => [
								GreaterThan::NOT_GREATER => 'The minimium donation amount is $1'
							]
						]
					]
				]
			],
			[
				'required' => false,
				'name' => 'recurring',
			],
			[
				'required' => true,
				'name' => 'first_name',
				'filters' => [
					['name' => 'StringTrim']
				],
				'validators' => [
					[
						'name' => 'NotEmpty',
						'options' => [
							'messages' => [
								NotEmpty::IS_EMPTY => 'First name is required.'
							]
						]
					],
				]
			],
			[
				'required' => true,
				'name' => 'last_name',
				'filters' => [
					['name' => 'StringTrim']
				],
				'validators' => [
					[
						'name' => 'NotEmpty',
						'options' => [
							'messages' => [
								NotEmpty::IS_EMPTY => 'Last name is required.'
							]
						]
					],
				]
			],
			[
				'required' => true,
				'name' => 'email',
				'filters' => [
					['name' => 'StringTrim']
				],
				'validators' => [
					[
						'name' => 'NotEmpty',
						'options' => [
							'messages' => [
								NotEmpty::IS_EMPTY => 'Email address is required.'
							]
						]
					],
					['name' => \Zend\Validator\EmailAddress::class]
				]
			],
			[
				'required' => true,
				'name' => 'cc_number',
				'filters' => [
					['name' => 'Digits']
				],
				'validators' => [
					[
						'name' => 'NotEmpty',
						'options' => [
							'messages' => [
								NotEmpty::IS_EMPTY => 'Credit card number is required.'
							]
						]
					],
					[
						'name' => 'CreditCard',
						'options' => [
							'messages' => [
								CreditCard::INVALID => 'Credit card number is required.',
								CreditCard::CHECKSUM => 'Invalid credit number.',
								CreditCard::CONTENT => 'Invalid credit number.',
								CreditCard::LENGTH => 'Invalid credit number.',
								CreditCard::PREFIX => 'Credit card must be Visa, Mastercard, Discover, or American Express.',
								CreditCard::SERVICE => 'Invalid credit number.',
								CreditCard::SERVICEFAILURE => 'Invalid credit number.',
							]
						]
					]
				]
			],
			[
				'required' => true,
				'name' => 'ccv',
				'filters' => [
					['name' => 'Digits']
				],
				'validators' => [
					[
						'name' => 'NotEmpty',
						'options' => [
							'messages' => [
								NotEmpty::IS_EMPTY => 'Security Code is required.'
							]
						]
					]
				]
			],
		];
	}
}

