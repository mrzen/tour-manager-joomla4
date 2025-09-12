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

		if (file_exists($queries)) {
			include $queries; // this should set $this->holidaysData

			$this->data = [
				'status' => 'ok',
				'count'  => isset($this->holidaysData) ? count($this->holidaysData) : 0,
				'items'  => $this->holidaysData ?? [],
			];
		} else {
			$this->data = [
				'status'  => 'error',
				'message' => 'Query file not found: ' . $queries,
			];
		}

		parent::display($tpl);
	}
}
