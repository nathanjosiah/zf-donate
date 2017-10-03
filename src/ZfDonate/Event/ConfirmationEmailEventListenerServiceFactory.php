<?php
namespace ZfDonate\Event;

use Zend\ServiceManager\Factory\FactoryInterface;

class ConfirmationEmailEventListenerServiceFactory implements FactoryInterface {
	public function __invoke(\Interop\Container\ContainerInterface $container,$requested_name,array $options = null) {
		$config = $container->get('Config')['zf-donate'];
		$listener = new ConfirmationEmailEventListener($container->get($config['email']['transport']),$container->get('ViewRenderer'),$config);
		return $listener;
	}
}