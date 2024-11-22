<?php

namespace RezKit\Component\RKTours\Site\View\Holiday;

use Error;
use Joomla\CMS\MVC\Controller\Exception\ResourceNotFound;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use RezKit\Tours\Client;

class HtmlView extends BaseHtmlView {

	public string $slug;
	protected $holiday;

	protected $client;

	public function display($tpl = null): void
	{
		$this->client = Client::create();

		$response = $this->client->query(<<<'GRAPHQL'
			query com_rktours_findHoliday($slug: String!) {
				holiday(slug: $slug) {
					id
					code
					name
					published
					search_public
					seo {
						meta_title
						meta_description
					}
				}
			}
		GRAPHQL,
		['slug' => $this->slug]);

		if ($response->hasErrors()) {
			// Unable to load holiday for some unknown reason.
			throw new Error('Unable to retrieve holiday details from RezKit Tour Manager', 502);
		}

		$holiday = $response->getData()['holiday'];

		if ($holiday === null) {
			throw new ResourceNotFound("No holiday found for the slug \"$this->slug\".", 404);
		}

		$this->item = $holiday;

		parent::display($tpl);
	}
}
