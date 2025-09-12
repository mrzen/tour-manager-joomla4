<?php

namespace RezKit\Component\RKTours\Site\Controller;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;

class DisplayController extends BaseController
{
	public function display($cachable = false, $urlparams = []): void
	{
		$app        = Factory::getApplication();
		$document   = $app->getDocument();
		$viewFormat = $document->getType(); // 'html', 'json', etc.
		$type       = $this->input->getCmd('type', 'holidays');
		$viewName   = $this->input->getCmd('view', $type);

		$view = $this->getView($viewName, $viewFormat);
		$view->document = $document;

		if ($viewFormat === 'html') {
			$view->slug   = $this->input->getString('slug');
			$view->id     = $this->input->getString('id');
			$view->setLayout($this->input->getCmd('layout', 'default'));
		}

		$view->display();
	}
}
