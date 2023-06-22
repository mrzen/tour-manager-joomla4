<?php

namespace RezKit\Tours;
class TourSync
{
	const LIST_HOLIDAYS_QUERY = /** @lang GraphQL */ <<<'QUERY'
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

	/**
	 * Get a list of all holidays.
	 * @return array List of all holidays
	 */
	public function getHolidayList(): array
	{
		$client = Client::create();
		$response = $client->query(self::LIST_HOLIDAYS_QUERY);

		$data = $response->getData();

		$holidays = array_map(fn (array $x) => $x['node'], $data['holidays']['edges']);

		while ($response->getData()['holidays']['pageInfo']['hasNextPage']) {

			$cursor = $response->getData()['holidays']['pageInfo']['endCursor'];
			$response = $client->query(self::LIST_HOLIDAYS_QUERY, ['cursor' => $cursor]);
			$pg = array_map(fn (array $x) => $x['node'], $data['holidays']['edges']);
			$holidays = array_merge($holidays, $pg);
		}

		return $holidays;
	}
}
