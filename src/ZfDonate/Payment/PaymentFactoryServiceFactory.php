<?php
namespace ZfDonate\Payment;

use Zend\ServiceManager\FactoryInterface;

class PaymentFactoryServiceFactory implements FactoryInterface {
	/**
	 * ZF2 Compatibility
	 */
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $container) {
		return $this($container,'');
	}

	public function __invoke(\Interop\Container\ContainerInterface $container,$requested_name,array $options = null) {
		$factory = new PaymentFactory($container);
		return $factory;
	}
}