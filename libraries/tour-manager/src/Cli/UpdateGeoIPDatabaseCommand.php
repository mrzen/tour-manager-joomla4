<?php

namespace RezKit\Tours\Cli;

use DateTimeInterface;
use Joomla\CMS\Factory;
use Joomla\Console\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateGeoIPDatabaseCommand extends AbstractCommand
{
	protected static $defaultName = 'rezkit:geoip:update';

	public function configure(): void
	{
		$this->setDescription('Update the MaxMind GeoIP Database');
		$this->addOption('force', 'f', InputOption::VALUE_NONE,
			'Force the database to be updated, even if it appears to be up-to-date');
	}

	protected function doExecute(InputInterface $input, OutputInterface $output): int
	{
		$updater = Factory::getContainer()->get('rezkit.geoip.updater');
		$force = $input->getOption('force');

		$output->writeln('Database was last updated: ' . $updater->getLastUpdated()?->format(DateTimeInterface::RFC7231));
		$output->writeln('Checking for newer available database updates.');

		if ($force || $updater->requiresUpdate()) {
			$output->writeln('Database requires updating, Downloading update');
			$updater->updateDatabase();
			$output->writeln('Database updated');
		} else {
			$output->writeln('Database appears to be up-to-date.');
		}

		return 0;
	}
}
