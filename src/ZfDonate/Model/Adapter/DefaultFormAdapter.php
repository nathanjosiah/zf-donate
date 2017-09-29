<?php
namespace ZfDonate\Model\Adapter;

use Zend\Form\FormInterface;
use Zend\Http\PhpEnvironment\Request;
use Zend\Hydrator\NamingStrategy\UnderscoreNamingStrategy;
use Zend\Hydrator\ObjectProperty;
use ZfDonate\Model\DonationEntity;

class DefaultFormAdapter implements FormAdapterInterface {
	public function hydrateForm(FormInterface $form, DonationEntity $donationEntity, array $data): void {
		$hydrator = new ObjectProperty();
		$hydrator->setNamingStrategy(new UnderscoreNamingStrategy());
		$form->setHydrator($hydrator);
		$form->bind($donationEntity);
		$form->setData($data);
	}
	public function hydrateEntity(FormInterface $form, DonationEntity $donationEntity): void {
		// The entity is automatically hydrated when isValid() is called because bind() is used above.
	}
	public function setDefaultData(FormInterface $form, Request $request, DonationEntity $donationEntity): void {
		// Nothing to do
	}
}