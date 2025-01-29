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
			'onAfterRoute' => 'configureServices',
		];
	}

	public function configureServices(): voi
	{
		$container = Factory::getContainer();

		$container->share(
			Client::class,
			static function (ContainerInterface $_) {
				return Client::create();
			}
		);

		$container->alias('rezkit.tours', Client::class);
	}
}
