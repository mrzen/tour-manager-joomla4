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
		$app      = Factory::getApplication();
		$template = $app->getTemplate();
		$queries  = JPATH_SITE . '/templates/' . $template . '/queries/holidays_query.php';

		$holidaysData = [];
		if (file_exists($queries)) {
			$holidaysData = include $queries;
		}

		if (!is_array($holidaysData)) {
			$holidaysData = [];
		}

		$this->data = [
			'status' => 'ok',
			'count'  => count($holidaysData),
			'items'  => $holidaysData,
		];

		parent::display();
	}
}
