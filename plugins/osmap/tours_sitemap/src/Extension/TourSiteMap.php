<?php
    namespace RezKit\Tours\Plugins\TourSiteMap\Extension;

    use Alledia\OSMap\Plugin\Base;
    use Alledia\OSMap\Sitemap\Collector;
    use Alledia\OSMap\Sitemap\Item;
    use Joomla\CMS\Log\Log;
    use Joomla\Registry\Registry;
    use RezKit\Tours\Client;

    class TourSiteMap extends Base
    {
        private const LIST_HOLIDAYS_QUERY = /** @lang GraphQL */ <<<'GRAPHQL'
            query tours_sitemap_listHolidays($cursor: String) {
                holidays(after: $cursor, first: 100) {
                    pageInfo {
                        hasNextPage
                        endCursor
                    }

                    edges {
                        node {
                            id
                            name
                            slug
                            published
                        }
                    }
                }
            }
        GRAPHQL;

        public function getComponentElement()
        {
            return 'com_rktours';
        }

        /**
         * Get a list of all holidays from the Tour Manager API.
         *
         * @return array List of all holidays
         * @since 1.0
         */
        public function getHolidayList(): array
        {
            $client = Client::create();

            $holidays = [];
            $cursor = null;

            do {
                $response = $client->query(self::LIST_HOLIDAYS_QUERY, ['cursor' => $cursor]);

                if ($response->hasErrors()) {
                    Log::add(
                        'Unable to retrieve holiday list from RezKit Tour Manager',
                        Log::ERROR,
                        'tours_sitemap'
                    );
                    break;
                }

                $page = $response->getData()['holidays'];

                foreach ($page['edges'] as $edge) {
                    $holidays[] = $edge['node'];
                }

                $cursor = $page['pageInfo']['endCursor'];
            } while (!empty($page['pageInfo']['hasNextPage']) && $cursor !== null);

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
            foreach ($this->getHolidayList() as $holiday) {
                if (($holiday['published'] ?? true) === false || empty($holiday['slug'])) {
                    continue;
                }

                $collector->changeLevel(1);

                $node = (object) array(
                    'id'   => $holiday['id'],
                    'uid'  => 'com_rktours.holiday.' . $holiday['id'],
                    'name' => $holiday['name'],
                    'link' => 'index.php?option=com_rktours&view=holiday&slug=' . $holiday['slug'],
                );

                $collector->printNode($node);
                $collector->changeLevel(-1);
            }
        }
    }
