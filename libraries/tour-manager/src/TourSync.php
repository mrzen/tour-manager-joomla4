<?php

namespace RezKit\Tours;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\Database\DatabaseInterface;

class TourSync
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

	public function sync(): array
	{
		return array_map(fn($h) => $this->syncHoliday($h), $this->getHolidayList());
	}

	private function syncHoliday(array $holiday): array
	{
		$this->log->info('Syncing Holiday ' . $holiday['id']);

		/**
		 * @var DatabaseInterface $db
		 */
		$db    = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true);

		// Find any existing record
		$query->select('id')
			->from('`#__holidays`')
			->where('rezkitid = :id')
			->bind(':id', $holiday['id']);

		$db->setQuery($query);

		$id = $db->loadResult();

		if ($id === null)
		{
			$query = $db->getQuery(true);

			$alias = ApplicationHelper::stringURLSafe($holiday['name']);

			$query->insert('`#__holidays`')
				->columns(['rezkitid', 'tourname', 'tourcode', 'alias'])
				->values(':id, :name, :code, :alias')
				->bind(':id', $holiday['id'])
				->bind(':name', $holiday['name'])
				->bind(':code', $holiday['code'])
				->bind(':alias', $alias);

			$db->setQuery($query);
			$db->execute();

			$id = $db->insertid();
		}

		return [
			'id'   => $id,
			'name' => $holiday['name'],
			'code' => $holiday['code'],
		];
	}
}
