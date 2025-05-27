<?php

namespace RezKit\Component\RKTours\Site\View\Accommodation;

use Error;
use Joomla\CMS\MVC\Controller\Exception\ResourceNotFound;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use RezKit\Tours\Client;

class HtmlView extends BaseHtmlView {

	public string $id;
	protected $accommodation;

	protected $client;

	public function display($tpl = null): void
	{
		$this->client = Client::create();

		$response = $this->client->query(<<<'GRAPHQL'
			query com_rktours_findAccommodation($id: ID!) {
				accommodation(id: $id) {
					id
					name
				}
			}
		GRAPHQL,
		['id' => $this->id]);

		if ($response->hasErrors()) {
			$errors = $response->getErrors();
			error_log('GraphQL Errors: ' . json_encode($errors));

			$errorMessage = 'Unable to retrieve accommodation details from RezKit Tour Manager.  GraphQL Errors: ' . json_encode($errors);
			throw new Error($errorMessage, 502);
		}

		$accommodation = $response->getData()['accommodation'];

		if ($accommodation === null) {
			throw new ResourceNotFound("No accommodation found for the id \"$this->id\".", 404);
		}

		$this->item = $accommodation;

		parent::display($tpl);
	}
}
