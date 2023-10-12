<?php
namespace RezKit\Tours\Plugins\Search;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\ApiRouter;
use Joomla\CMS\Router\SiteRouter;
use Joomla\Database\DatabaseInterface;

class TourSearch extends CMSPlugin
{
	public function onAjaxHolidayLinks(): array
	{
		$input = Factory::getApplication()->input;
		$ids = explode(',', $input->getString('ids'));

		/** @var DatabaseInterface $db */
		$db = Factory::getContainer()->get(DatabaseInterface::class);
		$q = $db->getQuery(true);

		$q->select('id')
			->from('#__holidays')
			->whereIn('rezkitid', $ids);

		$db->setQuery($q);
		$records = $db->loadColumn();

		/** @var SiteRouter $router */
		$router = Factory::getContainer()->get(SiteRouter::class);

		return array_map(static function (int $id) use ($router) {
			return $router->build('index.php?option=com_rktours&view=holiday&id=' . $id);
		}, $records);
	}
}
