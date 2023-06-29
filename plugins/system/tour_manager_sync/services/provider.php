<?php

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use RezKit\Tours\Plugins\Sync\Extension\SyncTasks;

require_once dirname(__FILE__) . '/../src/Extension/SyncTasks.php';

return new class() implements ServiceProviderInterface {
	public function register(Container $container)
	{
		$container->set(
			PluginInterface::class,
			function (Container $container) {
				$plugin = new SyncTasks(
					$container->get(DispatcherInterface::class),
					(array)PluginHelper::getPlugin('task', 'tour_manager_sync'),
				);

				$plugin->setApplication(Factory::getApplication());
				return $plugin;
			}
		);
	}
};
