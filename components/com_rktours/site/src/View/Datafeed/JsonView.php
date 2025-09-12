<?php
namespace RezKit\Component\RKTours\Site\View\Datafeed;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\View\JsonView as BaseJsonView;

class JsonView extends BaseJsonView
{
	public function display($tpl = null): void
	{
		$app   = Factory::getApplication();
		$input = $app->input;

		// Get expected API key from component params
		$params        = ComponentHelper::getParams('com_rktours');
		$expectedToken = $params->get('apikey');

		// Get provided token from request
		$providedToken = $input->getString('token');

		// Validate token
		if (!$expectedToken || $providedToken !== $expectedToken) {
			header('HTTP/1.1 403 Forbidden');
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode([
				'status'  => 'error',
				'message' => 'Invalid or missing token'
			], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
			$app->close();
		}

		// Determine the template and query file
		$template = $app->getTemplate();
		$queries  = JPATH_SITE . '/templates/' . $template . '/queries/data_feed_query.php';

		// Load feed data
		$dataFeed = [];
		if (file_exists($queries)) {
			$dataFeed = include $queries;
		}

		// Ensure it’s an array
		if (!is_array($dataFeed)) {
			$dataFeed = [];
		}

		// Prepare JSON response
		$response = [
			'status' => 'ok',
			'count'  => count($dataFeed),
			'items'  => $dataFeed
		];

		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

		exit;
	}
}
