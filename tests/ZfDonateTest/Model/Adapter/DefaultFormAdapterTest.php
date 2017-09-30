<?php
namespace ZfDonateTest\Model\Adapter;

use Zend\Form\Form;
use ZfDonate\Model\Adapter\DefaultFormAdapter;
use ZfDonate\Model\DonationEntity;

class DefaultFormAdapterTest extends \PHPUnit_Framework_TestCase {
	public function testHydrateForm() {
		$entity = new DonationEntity();
		$form = $this->getForm();

		$adapter = new DefaultFormAdapter();
		$adapter->hydrateForm($form,$entity,['first_name'=>'John']);
		$this->assertSame('John',$form->get('first_name')->getValue());
	}

	public function testHydrateEntity() {
		$entity = new DonationEntity();
		$form = $this->getForm();
		$adapter = new DefaultFormAdapter();

		$adapter->hydrateForm($form,$entity,['first_name'=>'John']);
		$form->isValid();
		$adapter->hydrateEntity($form,$entity);
		$this->assertSame('John',$entity->firstName);
	}

	public function testHydrateEntityWorksWithoutCallingHydrateForm() {
		$entity = new DonationEntity();
		$form = $this->getForm();
		$form->setData(['first_name'=>'John']);
		$form->isValid();

		$adapter = new DefaultFormAdapter();
		$adapter->hydrateEntity($form,$entity);
		$this->assertSame('John',$entity->firstName);
	}

	private function getForm() {
		$form = new Form();
		$form->add([
			'name' => 'first_name',
		]);
		return $form;
	}
}
