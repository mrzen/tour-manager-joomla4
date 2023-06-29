<?php

namespace RezKit\Tours\Cli;


use Joomla\Console\Command\AbstractCommand;
use RezKit\Tours\TourSync;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TourSyncCommand extends AbstractCommand
{
	protected static $defaultName = 'rezkit:tours:sync';

	public function configure(): void
	{
		$this->setDescription('Sync tour information from RezKit Tour Manager to Joomla');
	}

	protected function doExecute(InputInterface $input, OutputInterface $output): int
	{
		$io = new SymfonyStyle($input, $output);
		$sync = new TourSync();
		$sync->sync();
		return 0;
	}
}
