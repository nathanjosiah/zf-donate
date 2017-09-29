<?php

namespace ZfDonate\Payment;

use Zend\ServiceManager\ServiceLocatorInterface;
use ZfDonate\Payment\Gateway\OptionsAwareInterface;
use ZfDonate\Payment\Adapter\Exception\BadAdapterException;
use ZfDonate\Payment\DonationGateway;
use ZfDonate\Payment\Adapter\AdapterInterface;

class PaymentFactory {
	private $serviceLocator;

	public function __construct(ServiceLocatorInterface $service_locator) {
		$this->serviceLocator = $service_locator;
	}

	public function createGateway($configuration_name) : DonationGateway {
		$module_config = $this->serviceLocator->get('Config')['zf-donate'];

		$gateway_config = $module_config['configurations'][$configuration_name];

		$adapter_config = $module_config['adapters'][$gateway_config['adapter']];

		$adapter = $this->serviceLocator->get($adapter_config['adapter']);
		if(!$adapter instanceof AdapterInterface) {
			throw new BadAdapterException(sprintf('Adapter "%s" needs to implement %s.',$adapter_config['adapter'],AdapterInterface::class));
		}

		$gateway = $this->serviceLocator->get($adapter_config['gateway']);
		if($gateway instanceof OptionsAwareInterface) {
			$gateway->setOptions($gateway_config['options']);
		}
		$adapter->setGateway($gateway);

		return new DonationGateway($adapter,$configuration_name);
	}
}

