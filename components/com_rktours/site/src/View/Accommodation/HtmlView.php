<?php

namespace RezKit\Component\RKTours\Site\View\Accommodation;

use Error;
use Joomla\CMS\MVC\Controller\Exception\ResourceNotFound;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use RezKit\Tours\Client;

class HtmlView extends BaseHtmlView {

	public string $slug;
	protected $accommodation;

	protected $client;

	public function display($tpl = null): void
	{
		$this->client = Client::create();

		$response = $this->client->query(<<<'GRAPHQL'
			query com_rktours_findAccommodation($slug: String!) {
				accommodation(slug: $slug) {
					id
					name
				}
			}
		GRAPHQL,
		['slug' => $this->slug]);

		if ($response->hasErrors()) {
			throw new Error('Unable to retrieve accommodation details from RezKit Tour Manager', 502);
		}

		$accommodation = $response->getData()['accommodation'];

		if ($accommodation === null) {
			throw new ResourceNotFound("No accommodation found for the slug \"$this->slug\".", 404);
		}

		$this->item = $accommodation;

		parent::display($tpl);
	}
}
