<?php
namespace RezKit\Component\RKTours\Site\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

class DataFeedController extends BaseController
{
	public function display($cachable = false, $urlparams = []): void
	{
		$document = \Joomla\CMS\Factory::getApplication()->getDocument();
		$viewFormat = 'json';
		$viewName = 'datafeed';

		$view = $this->getView($viewName, $viewFormat);
		$view->document = $document;

		$view->display();
	}
}
