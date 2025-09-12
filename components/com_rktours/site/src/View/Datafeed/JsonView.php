<?php
namespace RezKit\Component\RKTours\Site\View\Datafeed;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\JsonView as BaseJsonView;

class JsonView extends BaseJsonView
{
	protected $data;

	public function display($tpl = null): void
	{
		// Determine the template and query file
		$app      = Factory::getApplication();
		$template = $app->getTemplate();
		$queries  = JPATH_SITE . '/templates/' . $template . '/queries/holidays_query.php';

		// Load holiday data
		$holidaysData = [];
		if (file_exists($queries)) {
			$holidaysData = include $queries;
		}

		// Ensure it’s an array
		if (!is_array($holidaysData)) {
			$holidaysData = [];
		}

		// Prepare JSON response
		$response = [
			'status' => 'ok',
			'count'  => count($holidaysData),
			'items'  => $holidaysData
		];

		header('Content-Type: application/json; charset=utf-8');
		// Encode and output JSON
		echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		exit;
	}
}
