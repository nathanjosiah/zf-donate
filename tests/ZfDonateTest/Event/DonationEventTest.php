<?php
namespace ZfDonateTest\Event;

use Zend\Form\Form;
use ZfDonate\Event\DonationEvent;
use ZfDonate\Model\DonationEntity;

class DonationEventTest extends \PHPUnit\Framework\TestCase {
	public function testParametersWorkWithSetters() {
		$event = new DonationEvent();
		$entity = new DonationEntity();
		$form = new Form();
		$event->setDonationEntity($entity);
		$event->setDonationForm($form);

		$this->assertSame($entity,$event->getParam('entity'));
		$this->assertSame($form,$event->getParam('form'));
	}

	public function testGettersWorkWithParameters() {
		$event = new DonationEvent();
		$entity = new DonationEntity();
		$form = new Form();
		$event->setParam('entity',$entity);
		$event->setParam('form',$form);
		$this->assertSame($entity,$event->getDonationEntity());
		$this->assertSame($form,$event->getDonationForm());
	}

	public function testGettersWorkWithSetters() {
		$event = new DonationEvent();
		$entity = new DonationEntity();
		$form = new Form();

		$event->setDonationEntity($entity);
		$event->setDonationForm($form);

		$this->assertSame($entity,$event->getDonationEntity());
		$this->assertSame($form,$event->getDonationForm());
	}
}
