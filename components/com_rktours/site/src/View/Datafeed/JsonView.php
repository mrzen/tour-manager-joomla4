<?php
namespace RezKit\Component\RKTours\Site\View\Datafeed;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\JsonView as BaseJsonView;

class JsonView extends BaseJsonView
{
	protected $data;

	public function display($tpl = null): void
	{
		$this->data = [
			'status' => 'ok',
			'count' => 2,
			'items' => [
				['id' => 1, 'slug' => 'holiday-a', 'name' => 'Holiday A'],
				['id' => 2, 'slug' => 'holiday-b', 'name' => 'Holiday B']
			]
		];

		parent::display();
	}
}
