<?php

namespace RezKit\Component\RKTours\Site\Extension;

use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Extension\MVCComponent;

class RezKitComponent extends MVCComponent implements RouterServiceInterface
{
	use RouterServiceTrait;
}
