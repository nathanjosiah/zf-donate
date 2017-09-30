<?php
namespace ZfDonate\Event;


use Zend\EventManager\Event;
use Zend\Form\FormInterface;
use ZfDonate\Model\DonationEntity;

class DonationEvent extends Event  {
	public function setDonationEntity(DonationEntity $donationEntity) : void {
		$this->setParam('entity',$donationEntity);
	}

	public function getDonationEntity() : DonationEntity {
		return $this->getParam('entity');
	}

	public function setDonationForm(FormInterface $form) : void {
		$this->setParam('form',$form);
	}

	public function getDonationForm() : FormInterface {
		return $this->getParam('form');
	}
}