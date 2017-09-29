<?php
namespace ZfDonate\Event;


use Zend\ServiceManager\Factory\FactoryInterface;

class ConfirmationRedirectListenerServiceFactory implements FactoryInterface {
	public function __invoke(\Interop\Container\ContainerInterface $container,$requested_name,array $options = null) {
		$config = $container->get('Config')['zf-donate'];
		$listener = new ConfirmationRedirectListener($config);
		return $listener;
	}
}