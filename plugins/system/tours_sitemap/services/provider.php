<?php

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\CMS\Console\Loader\WritableLoaderInterface;
use Joomla\CMS\Http\HttpFactory;
use RezKit\Tours\Plugins\TourSiteMap\Extension\TourSiteMap;

require_once JPATH_LIBRARIES . '/tour-manager/vendor/autoload.php';

return new class() implements ServiceProviderInterface {
	/**
	 * Register container services
	 *
	 * @param   Container  $container
	 * @return void
	 * @since 0.1.0
	 */
	public function register(Container $container): void
	{

		$container->set(
			PluginInterface::class,
			function (Container $container) {
				$subject = $container->get(DispatcherInterface::class);

				$plugin = new TourSiteMap(
					$subject,
					(array) PluginHelper::getPlugin('system', 'tours_sitemap'),
				);

				$plugin->setApplication(Factory::getApplication());

				return $plugin;
			}
		);


	}
};
