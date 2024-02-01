<?php
    namespace RezKit\Tours\Plugins\TourSiteMap\Extension;

    use Alledia\OSMap\Plugin\Base;
    use Alledia\OSMap\Sitemap\Collector;
    use Alledia\OSMap\Sitemap\Item;
    use Joomla\Registry\Registry;
    use Joomla\CMS\Log\Log;

    class TourSiteMap extends Base
    {
        const LIST_HOLIDAYS_QUERY = /** @lang GraphQL */
		<<<'QUERY'
		query syncHolidayList($cursor: String) {
			holidays(after: $cursor, first: 100) {
				pageInfo {
					hasNextPage
					endCursor
				}
				
				edges {
					node {
						id
						name
						code
					}
				}
			}	
		}
	QUERY;

	private \Softonic\GraphQL\Client $client;

	private \Joomla\CMS\Log\DelegatingPsrLogger $log;

	public function __construct()
	{
		$this->client = Client::create();
		$this->log    = Log::createDelegatedLogger();
	}

	/**
	 * Get a list of all holidays.
	 *
	 * @return array List of all holidays
	 * @since 1.0
	 */
	public function getHolidayList(): array
	{
		$response = $this->client->query(self::LIST_HOLIDAYS_QUERY);

		$data = $response->getData();

		$holidays = array_map(fn(array $x) => $x['node'], $data['holidays']['edges']);

		while ($response->getData()['holidays']['pageInfo']['hasNextPage'])
		{

			$cursor   = $response->getData()['holidays']['pageInfo']['endCursor'];
			$response = $this->client->query(self::LIST_HOLIDAYS_QUERY, ['cursor' => $cursor]);

			$pg       = array_map(fn(array $x) => $x['node'], $data['holidays']['edges']);
			$holidays = array_merge($holidays, $pg);
		}

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

            // foreach($holidays as $holiday) {
            //     $node = (object)array(
            //         'id'         => $parent->id,
            //         'name'       => 'This Node Title',
            //         'uid'        => $parent->uid . '_' . 'UniqueNodeId',
            //         'modified'   => '2018-02-28 12:00:00',
            //         'browserNav' => $parent->browserNav,
            //         'priority'   => .5,
            //         'changefreq' => 'weekly',
            //         'link'       => 'index.php?option=com_mycomponent'
            //     );

            //     $collector->printNode($node);
            // }
            
        }
    }
?>