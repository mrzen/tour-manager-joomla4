<?php
    namespace RezKit\Tours\Plugins\TourSiteMap\Extension;

    use Alledia\OSMap\Plugin\Base;
    use Alledia\OSMap\Sitemap\Collector;
    use Alledia\OSMap\Sitemap\Item;
    use Joomla\Registry\Registry;
    use Joomla\CMS\Factory;
    use Joomla\Database\DatabaseInterface;

    class TourSiteMap extends Base
    {
      
	/**
	 * Get a list of all holidays.
	 *
	 * @return array List of all holidays
	 * @since 1.0
	 */
	public function getHolidayList(): array
	{
		/** @var DatabaseInterface $db */
		$db = Factory::getContainer()->get(DatabaseInterface::class);
		$q = $db->getQuery(true);

		$q = $q->select('id', 'rezkitid', 'tourname', 'tourcode', 'alias')
			->from('#__holidays')
			->order('id');

		$db->setQuery($q);
		$holidays = $db->loadObjectList('rezkitid');

		return $holidays;
	}

        /**
         * @param Collector $collector
         * @param Item      $parent
         * @param Registry  $params
         *
         * @return void
         */
        public function getTree(Collector $collector, Item $parent, Registry $params)
        {
            $holidays = $this->getHolidayList();

            var_dump($holidays);

            foreach($holidays as $holiday) {
                $node = (object)array(
                    'id'         => $holiday->id,
                    'name'       => $holiday->name,
                    'uid'        => $holiday->rezkitid,
                    'link'       => 'index.php?option=rk_tours&view=holiday&id=' . $holiday->id,
                );

                $collector->printNode($node);
            }
            
        }
    }
?>