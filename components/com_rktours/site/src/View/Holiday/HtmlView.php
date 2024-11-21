<?php

namespace RezKit\Component\Tours\Site\View\Holidays;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use RezKit\Tours\Client;

class HtmlView extends BaseHtmlView {

	protected $item;

	public function display($tpl = null) {

		$client = Client::create();

		$holiday = $client->query(<<<'GRAPHQL'
			query findHoliday($slug: String!) {
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
