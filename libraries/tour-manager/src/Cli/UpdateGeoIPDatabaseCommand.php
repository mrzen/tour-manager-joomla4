<?php

namespace RezKit\Tours\Cli;

use Joomla\CMS\Factory;
use Joomla\Console\Command\AbstractCommand;
use Joomla\Http\Http;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UpdateGeoIPDatabaseCommand extends AbstractCommand
{
	protected static $defaultName = 'rezkit:geoip:update';

	public function configure(): void
	{
		$this->setDescription('Update the MaxMind® GeoIP Database');
		$this->addOption('force', 'f', InputArgument::OPTIONAL,
			'Force the database to be updated, even if it appears to be up-to-date');
	}

	protected function doExecute(InputInterface $input, OutputInterface $output): int
	{
		$io = new SymfonyStyle($input, $output);
		$force = $input->getArgument('force');

		if ($force || $this->requiresUpdate()) {
			$io->info('Database requires updating. Downloading update');

			$http = Factory::getContainer()->get(Http::class);
		}
	}

	private function requiresUpdate(): bool
	{
		return true;
	}
}
