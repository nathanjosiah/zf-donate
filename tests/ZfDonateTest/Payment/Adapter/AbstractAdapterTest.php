<?php
namespace ZfDonateTest\Payment\Adapter;

use Omnipay\Common\CreditCard;
use ZfDonate\Model\DonationEntity;
use ZfDonate\Payment\Adapter\AbstractAdapter;

class AbstractAdapterTest extends \PHPUnit_Framework_TestCase {
	public function testOptionsAreMapped() {
		$stub = new AbstractAdapterStub();
		$donation = new DonationEntity();
		$donation->amount = 12.34;
		$donation->firstName = 'firstName';
		$donation->lastName = 'lastName';
		$donation->email = 'email';
		$donation->address = 'billingAddress1';
		$donation->city = 'billingCity';
		$donation->postalCode = 'billingPostcode';
		$donation->state = 'billingState';
		$donation->phone = 'shippingPhone';
		$donation->address = 'shippingAddress1';
		$donation->city = 'shippingCity';
		$donation->postalCode = 'shippingPostcode';
		$donation->state = 'shippingState';
		$donation->phone = 'shippingPhone';
		$donation->ccNumber = '4111111111111111';
		$donation->ccExpirationMonth = '9';
		$donation->ccExpirationYear = '' . idate('Y')+1;
		$donation->ccV = '123';

		$expected = [
			'email' => $donation->email,
			'billingAddress1' => $donation->address,
			'billingCity' => $donation->city,
			'billingPostcode' => $donation->postalCode,
			'billingState' => $donation->state,
			'billingCountry' => 'US',
			'shippingPhone' => $donation->phone,
			'shippingAddress1' => $donation->address,
			'shippingCity' => $donation->city,
			'shippingPostcode' => $donation->postalCode,
			'shippingState' => $donation->state,
			'shippingCountry' => 'US',
			'shippingPhone' => $donation->phone,
			'number' => $donation->ccNumber,
			'expiryMonth' => (int)$donation->ccExpirationMonth,
			'expiryYear' => (int)$donation->ccExpirationYear,
			'cvv' => $donation->ccV,
			'billingFirstName' => 'firstName',
			'shippingFirstName' => 'firstName',
			'billingLastName' => 'lastName',
			'shippingLastName' => 'lastName',
		];
		$base_options = $stub->getOptions($donation);
		$this->assertSame(12.34,$base_options['amount']);
		$this->assertInstanceOf(CreditCard::class,$base_options['card']);
		$this->assertEquals($expected,$base_options['card']->getParameters());
	}
}
