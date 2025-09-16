<?php

namespace RezKit\Component\RKTours\Site\View\Holiday;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\View\JsonView as BaseJsonView;

class JsonView extends BaseJsonView
{
	protected $holiday;
	public function display($tpl = null): void
	{
		$app   = Factory::getApplication();
		$input = $app->input;
		$id    = $input->getString('holiday_id');
		$slug  = $input->getString('slug');

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

		$template = $app->getTemplate();
		$queries = JPATH_SITE . '/templates/' . $template . '/queries/holiday_query.php';
		$this->item = ['id' => $id];

		if (file_exists($queries)) {
			include $queries;
		}

		// Prepare JSON response
		$response = [
			'status' => 'ok',
			// 'this'  => $this,
			'item' => $this->holidayData['holiday'] ?? [],
		];

		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

		exit;
	}
}
