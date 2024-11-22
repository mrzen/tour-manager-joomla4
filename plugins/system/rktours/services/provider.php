<?php

use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use RezKit\Tours\Client;

require_once JPATH_LIBRARIES . '/tour-manager/vendor/autoload.php';

return new class implements ServiceProviderInterface {

	public function register(Container $container)
	{
		$container->set(Client::class, Client::create());
		$container->alias('rezkit.client', Client::class);
	}
};
