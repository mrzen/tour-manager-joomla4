<?php
namespace RezKit\Tours\Plugins\Search\Extension;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\SiteRouter;
use Joomla\Database\DatabaseInterface;
use Joomla\Database\ParameterType;

class TourSearch extends CMSPlugin
{
	/**
	 * @return array<string, object> Holiday routings
	 * @since 0.1.0
	 */
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

	/**
	 * @return array<string,object> Review Scores
	 * @since 0.1.0
	 */
	public function onAjaxHolidayreviews(): array
	{
		$input = Factory::getApplication()->input;
		$codes = explode(',', $input->getString('codes'));

		$db = Factory::getContainer()->get(DatabaseInterface::class);
		$q = $db->getQuery(true);

		$q = $q->select('trip_code, COUNT(*) as count, AVG(rating) as score')
			->from('#__ugc_reviews')
			->whereIn('trip_code', $codes, ParameterType::STRING)
			->where('state = 1')
			->where('rating > 0')
			->group('trip_code');

		$db->setQuery($q);
		return $db->loadObjectList('trip_code');
	}
}
