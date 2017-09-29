<?php
namespace ZfDonate\Event;


use Zend\ServiceManager\FactoryInterface;

class ConfirmationEmailEventListenerServiceFactory implements FactoryInterface {
	/**
	 * ZF2 Compatibility
	 */
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $container) {
		return $this($container,'');
	}

	public function __invoke(\Interop\Container\ContainerInterface $container,$requested_name,array $options = null) {
		$config = $container->get('Config')['zf-donate'];
		$listener = new ConfirmationEmailEventListener($container->get($config['email']['transport']),$container->get('View'),$config);
		return $listener;
	}
}