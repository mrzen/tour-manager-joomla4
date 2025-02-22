<?php

namespace RezKit\Component\RKTours\Site\Service;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Menu\AbstractMenu;

class Router extends RouterView {

	public function __construct(SiteApplication $app, AbstractMenu $menu)
	{
		$holidays = new RouterViewConfiguration('holidays');
		$this->registerView($holidays);

		$holiday =  new RouterViewConfiguration('holiday');
		$holiday->setKey('slug')->setParent($holidays);
		$this->registerView($holiday);

		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));
		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));
	}

	public function getHolidayId($slug, &$query): string
	{
		$query['slug']  = $slug;
		return $slug;
	}
}
