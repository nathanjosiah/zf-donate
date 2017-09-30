<?php

namespace ZfDonateTest\Form;

use ZfDonate\Form\DonateForm;

class DonateFormTest extends \PHPUnit_Framework_TestCase {
	public function testAllFieldsExist() {
		$form = new DonateForm();
		$form->init();
		$fields = [
			'amount',
			'recurring',
			'first_name',
			'last_name',
			'email',
			'cc_number',
			'cc_expiration_month',
			'cc_expiration_year',
			'ccv'
		];
		foreach($fields as $field) {
			$this->assertTrue($form->has($field),'Field ' . $field . ' doesn\'t exist');
		}
	}

	public function testFieldsAreRequired() {
		$form = new DonateForm();
		$form->init();
		$fields = [
			'first_name',
			'last_name',
			'amount',
			'email',
			'cc_number',
			'cc_expiration_month',
			'cc_expiration_year',
			'ccv'
		];
		$form->setData([]);

		$this->assertFalse($form->isValid(),'Form shouldn\'t be valid');
		$errors = $form->getMessages();
		// Order of errors shouldn't matter
		foreach($errors as $key => $na) {
			$this->assertContains($key,$fields);
		}
	}

	public function testValidSubmission() {
		$form = new DonateForm();
		$form->init();
		$data = [
			'first_name' => 'John',
			'last_name' => 'Doe',
			'amount' => '12.34',
			'email' => 'foobar@example.com',
			'cc_number' => '4111 1111 1111 1111',
			'cc_expiration_month' => '01',
			'cc_expiration_year' => '' . idate('Y')+1,
			'ccv' => '123'
		];
		$form->setData($data);

		$this->assertTrue($form->isValid(),'Form should be valid');
	}

	public function testDataNormalization() {
		$form = new DonateForm();
		$form->init();
		$data = [
			'first_name' => 'John',
			'last_name' => 'Doe',
			'amount' => '$abc12.34.55abc',
			'email' => 'foobar@example.com',
			'cc_number' => '    4111 1111 1111 1111   ',
			'cc_expiration_month' => '01',
			'cc_expiration_year' => '' . idate('Y')+1,
			'ccv' => '   123   '
		];
		$form->setData($data);
		$this->assertTrue($form->isValid(),'Form should be valid');
		$normalized = $form->getData();
		$this->assertSame('4111111111111111',$normalized['cc_number']);
		$this->assertSame(12.34,$normalized['amount']);
		$this->assertSame('123',$normalized['ccv']);
	}
}
