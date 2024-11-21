<?php

use Joomla\CMS\Component\Router\RouterFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class implements ServiceProviderInterface
{
	public function register(Container $container):  void
	{
		$container->registerServiceProvider(new RouterFactory('RezKit\\Tours\\Joomla\\Site\\Services'));
	}
};
