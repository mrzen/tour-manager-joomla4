<?php

namespace RezKit\Tours\Plugins\Sync\Extension;

use Joomla\Application\ApplicationEvents;
use Joomla\CMS\Console\Loader\WritableLoaderInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Component\Scheduler\Administrator\Event\ExecuteTaskEvent;
use Joomla\Component\Scheduler\Administrator\Task\Status;
use Joomla\Component\Scheduler\Administrator\Traits\TaskPluginTrait;
use Joomla\Event\SubscriberInterface;
use Psr\Container\ContainerInterface;
use RezKit\Tours\Cli\TourSyncCommand;
use RezKit\Tours\Client;
use RezKit\Tours\TourSync;

final class SyncTasks extends CMSPlugin implements SubscriberInterface
{
	use TaskPluginTrait;

	private const TASKS_MAP = [
		'tour_manager.sync' => [
			'langConstPrefix' => 'PLG_TOUR_MANAGER_TASK_SYNC',
			'method'          => 'tourSync',
			'form'            => 'tour_sync',
		]
	];

	protected $autoloadLanguage = true;

	public static function getSubscribedEvents(): array
	{
		return [
			'onTaskOptionsList'    => 'advertiseRoutines',
			'onExecuteTask'        => 'standardRoutineHandler',
			'onContentPrepareForm' => 'enhanceTaskItemForm',
			ApplicationEvents::BEFORE_EXECUTE => 'registerServices',
		];
	}

	public function registerServices(): void
	{

		Factory::getContainer()->share(
			'rezkit.tours.sync',
			function (ContainerInterface $container) {
				return new TourSyncCommand();
			},
			true
		);

		Factory::getContainer()->get(WritableLoaderInterface::class)->add('rezkit:tours:sync', 'rezkit.tours.sync');

		// Share the API Client.
		Factory::getContainer()->share(
			'rezkit.tours',
			static function (ContainerInterface $container) {
				return Client::create();
			}
		);
	}

	private function tourSync(ExecuteTaskEvent $event): int
	{
		$sync = new TourSync();
		$this->logTask("Starting Tour Sync");

		$holidays = $sync->sync();

		return Status::OK;
	}
}
