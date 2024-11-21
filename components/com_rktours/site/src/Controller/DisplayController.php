<?php

namespace RezKit\Component\RKTours\Site\Controller;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

class DisplayController extends BaseController
{
	public function display($cachable = false, $urlparams = []): void
	{
		$document = Factory::getApplication()->getDocument();
		$viewName = $this->input->getCmd('view','holidays');
		$viewFormat = $document->getType();
		$view  = $this->getView($viewName, $viewFormat);
		$view->document = $document;

		$view->display();
	}
}
