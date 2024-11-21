<?php

namespace RezKit\Tours\Joomla\Site\Services;

use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Menu\AbstractMenu;

class Router extends RouterView {

	public function __construct(SiteApplication $app, AbstractMenu $menu)
	{
		$params = ComponentHelper::getParams('com_rktours');

		$holidays = new RouterViewConfiguration('holidays');
		$this->registerView($holidays);

		$holiday =  new RouterViewConfiguration('holiday');
		$this->registerView($holiday);

		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));
		$this->attachRule(new StandardRules($this));
		$this->attachRule(new NomenuRules($this));
	}

}