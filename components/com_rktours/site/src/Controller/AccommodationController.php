<?php

namespace RezKit\Component\RKTours\Site\Controller;

use Joomla\CMS\MVC\Controller\BaseController;

class AccommodationController extends BaseController
{
	public function display($cachable = false, $urlparams = [])
	{
		$this->input->set('type', 'accommodation');

		return parent::display($cachable, $urlparams);
	}
}
