<?php

namespace RezKit\Tours\Plugins\GeoIP\Extension;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Component\Scheduler\Administrator\Event\ExecuteTaskEvent;
use Joomla\Component\Scheduler\Administrator\Task\Status;
use Joomla\Component\Scheduler\Administrator\Traits\TaskPluginTrait;
use Joomla\Event\SubscriberInterface;

class GeoIP extends CMSPlugin implements SubscriberInterface
{
	use TaskPluginTrait;

	private const TASKS_MAP = [
		'tour_manager.geoip_update' => [
			'langConstPrefix' => 'PLG_REZKIT_GEOIP_TASK_UPDATE',
			'method' => 'updateGeoIPDatabase',
			'form' => 'update_geoip_database'
		]
	];

	public static function getSubscribedEvents(): array
	{
		return [
			'onTaskOptionsList' => 'advertiseRoutines',
			'onAfterInitialise' => 'handleRequest'
		];
	}

	/**
	 * Handle an incoming request
	 *
	 * @return void
	 * @since 0.1.0
	 */
	public function handleRequest(): void
	{
		//$session = Factory::getContainer()->get('session');
	}

	private function updateGeoIPDatabase(ExecuteTaskEvent $event): int
	{
		return Status::OK;
	}
}
