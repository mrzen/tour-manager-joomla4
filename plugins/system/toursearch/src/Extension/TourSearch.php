<?php
namespace RezKit\Tours\Plugins\Search\Extension;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\SiteRouter;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

class TourSearch extends CMSPlugin
{
	public function onAjaxHolidaylinks(): array
	{
		$input = Factory::getApplication()->input;
		$ids = explode(',', $input->getString('ids'));

		/** @var DatabaseInterface $db */
		$db = Factory::getContainer()->get(DatabaseInterface::class);
		$q = $db->getQuery(true);

		$q = $q->select('id, rezkitid')
			->from('#__holidays')
			->whereIn('rezkitid', $ids, ParameterType::STRING)
			->order('id');

		$db->setQuery($q);
		$records = $db->loadObjectList('rezkitid');

		/** @var SiteRouter $router */
		$router = Factory::getContainer()->get(SiteRouter::class);

		foreach ( $records as &$record ) {
			$record->url = $router->build('/?index.php&option=com_rktours&view=holiday&id=' . $record->id)->toString(['path']);
		}

		return $records;
	}
}
