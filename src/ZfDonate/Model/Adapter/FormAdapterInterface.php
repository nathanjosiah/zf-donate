<?php
namespace ZfDonate\Model\Adapter;

use ZfDonate\Model\DonationEntity;
use Zend\Form\FormInterface;

interface FormAdapterInterface {
	public function hydrateForm(FormInterface $form,array $data) : void;
	public function hydrateEntity(FormInterface $form,DonationEntity $donationEntity) : void;
}