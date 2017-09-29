<?php
namespace ZfDonate\Payment;

use Zend\ServiceManager\Factory\FactoryInterface;

class PaymentFactoryServiceFactory implements FactoryInterface {
	public function __invoke(\Interop\Container\ContainerInterface $container,$requested_name,array $options = null) {
		$factory = new PaymentFactory($container);
		return $factory;
	}
}