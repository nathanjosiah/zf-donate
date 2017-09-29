<?php
namespace ZfDonate\Model\Adapter;

use Zend\Http\PhpEnvironment\Request;
use ZfDonate\Model\DonationEntity;
use Zend\Form\FormInterface;

interface FormAdapterInterface {
	public function hydrateForm(FormInterface $form,DonationEntity $donationEntity, array $data) : void;
	public function hydrateEntity(FormInterface $form,DonationEntity $donationEntity) : void;
	public function setDefaultData(FormInterface $form,Request $request,DonationEntity $donationEntity) : void;
}