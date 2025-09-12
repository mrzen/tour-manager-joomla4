<?php
namespace RezKit\Component\RKTours\Site\View\Datafeed;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\JsonView as BaseJsonView;

class JsonView extends BaseJsonView
{
	protected $data;

	public function display($tpl = null): void
	{
		$app      = \Joomla\CMS\Factory::getApplication();
		$template = $app->getTemplate();
		$queries  = JPATH_SITE . '/templates/' . $template . '/queries/holidays_query.php';

		if (file_exists($queries)) {
			$holidaysData = include $queries;

			$this->data = [
				'status' => 'ok',
				'count'  => count($holidaysData),
				'items'  => $holidaysData,
			];
		} else {
			$this->data = [
				'status'  => 'error',
				'message' => 'Query file not found: ' . $queries,
			];
		}

		parent::display();
	}

}
