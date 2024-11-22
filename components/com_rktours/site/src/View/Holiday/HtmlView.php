<?php

namespace RezKit\Component\RKTours\Site\View\Holiday;

use Error;
use Joomla\CMS\MVC\Controller\Exception\ResourceNotFound;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use RezKit\Tours\Client;

class HtmlView extends BaseHtmlView {

	protected $item;

	public function display($tpl = null): void
	{
		$client = Client::create();

		$response = $client->query(<<<'GRAPHQL'
			query com_rktours_findHoliday($slug: String!) {
				holiday(slug: $slug) {
					id
					code
					name
					published
					search_public
				}
			}
		GRAPHQL
		);

		if ($response->hasErrors()) {
			// Unable to load holiday for some unknown reason.
			throw new Error('Unable to retrieve holiday details from RezKit Tour Manager');
		}

		$holiday = $response->getData()['holiday'];

		if ($holiday === null) {
			throw new ResourceNotFound("No holiday found for given slug");
		}

		$this->item = $holiday;

		parent::display($tpl);
	}
}
