<?php

namespace RezKit\Plugins\System\RKTours\Extension;

use Joomla\Application\ApplicationEvents;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Psr\Container\ContainerInterface;
use RezKit\Tours\Client;

class TourManagerPlugin extends CMSPlugin implements SubscriberInterface
{

	public static function getSubscribedEvents(): array
	{
		return [
			ApplicationEvents::BEFORE_EXECUTE => 'configureServices'
		];
	}

	public function configureServices(): void
	{
		$container = Factory::getContainer();

		$container->share(
			Client::class,
			static function (ContainerInterface $container) {
				return Client::create();
			}
		);

		$container->alias('rezkit.tours', Client::class);
	}
}
