<?php
namespace ZfDonate\Model\Adapter;

use Zend\Form\FormInterface;
use Zend\Http\PhpEnvironment\Request;
use Zend\Hydrator\HydratorInterface;
use Zend\Hydrator\NamingStrategy\UnderscoreNamingStrategy;
use Zend\Hydrator\ObjectProperty;
use ZfDonate\Model\DonationEntity;

class DefaultFormAdapter implements FormAdapterInterface {
	public function hydrateForm(FormInterface $form, DonationEntity $donationEntity, array $data): void {
		$form->setHydrator($this->getHydrator());
		$form->bind($donationEntity);
		$form->setData($data);
	}
	public function hydrateEntity(FormInterface $form, DonationEntity $donationEntity): void {
		if($form->getObject()) {
			// The entity is automatically hydrated when isValid() is called because bind() is used above.
			return;
		}
		$data = $form->getData();
		$this->getHydrator()->hydrate($data,$donationEntity);
	}

	public function setDefaultData(FormInterface $form, Request $request, DonationEntity $donationEntity): void {
		// Nothing to do
	}

	private function getHydrator() :HydratorInterface {
		$hydrator = new ObjectProperty();
		$hydrator->setNamingStrategy(new UnderscoreNamingStrategy());
		return $hydrator;
	}
}