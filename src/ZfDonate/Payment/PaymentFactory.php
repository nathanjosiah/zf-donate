<?php

namespace ZfDonate\Payment;

use Zend\ServiceManager\ServiceLocatorInterface;
use ZfDonate\Payment\Gateway\OptionsAwareInterface;

class PaymentFactory {
	private $serviceLocator;

	public function __construct(ServiceLocatorInterface $service_locator) {
		$this->serviceLocator = $service_locator;
	}

	public function createGateway($configuration_name) : DonationGateway {
		$module_config = $this->serviceLocator->get('Config')['zf-donate'];

		$gateway_config = $module_config['configurations'][$configuration_name];

		$adapter_config = $module_config['gateways'][$gateway_config['gateway']];

		$adapter = $this->serviceLocator->get($adapter_config['adapter']);

		$gateway = $this->serviceLocator->get($adapter_config['gateway']);
		if($gateway instanceof OptionsAwareInterface) {
			$gateway->setOptions($gateway_config['options']);
		}
		$adapter->setGateway($gateway);

		return new DonationGateway($adapter,$configuration_name);
	}

	public function getServiceLocator() : ServiceLocatorInterface {
		return $this->serviceLocator;
	}
}

