<?php

namespace RezKit\Component\RKTours\Site\Controller;

use Joomla\CMS\MVC\Controller\BaseController;

class AccommodationController extends BaseController
{
	public function display($cachable = false, $urlparams = [])
	{
		return parent::display($cachable, $urlparams, 'accommodations');
	}
}
