<?php
namespace RezKit\Tours\Plugins\Search;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\ApiRouter;

class Plugin extends CMSPlugin
{
	public function onBeforeApiRoute(ApiRouter &$router): void
	{
		$router->get('v1/tour-search/links', Controller::class);
	}
}
