<?php

namespace RezKit\Component\RKTours\Site\View\Holidays;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use RezKit\Tours\Client;

class HtmlView extends BaseHtmlView {

	protected $item;

	public function display($tpl = null): void
	{

		$client = Client::create();

		$holiday = $client->query(<<<'GRAPHQL'
			query com_rktours_findHoliday($slug: String!) {
				holiday(slug: $slug) {
					id
					published
				}
			}
		GRAPHQL
		);

		parent::display($tpl);
	}
}
