<?php

namespace FIW\Models;

use \Slim\Slim;
use ORM;

class TextModel
{

	public function getText($slug)
	{
		$found = ORM::for_table('texts')
			->where('slug', $slug)
			->find_one();

		if (is_object($found))
			return $this->tagText($found->text);

		return '';
	}

	public function getContacts()
	{
		$found = ORM::for_table('texts')
			->where_like('slug', 'contacts_%')
			->find_array();

		$data = [
			'contacts' => [],
			'emails' => [],
			'address' => []
		];

		foreach($found as $item) {
			$key = explode('_', $item['slug']);

			if (count($key) != 2 || $key[0] != 'contacts')
				continue;

			$data[$key[1]] = explode("\n", $item['text']);
		}

		return $data;
	}

	public function getSocial()
	{
		$found = ORM::for_table('texts')
			->where_like('slug', 'social_%')
			->find_array();

		$data = [
			'twitter' => '',
			'pinterest' => '',
			'facebook' => '',
			'vk' => '',
			'gp' => ''
		];

		foreach($found as $item) {
			$key = explode('_', $item['slug']);

			if (count($key) != 2 || $key[0] != 'social')
				continue;

			$data[$key[1]] = $item['text'];
		}

		return $data;
	}


	private function tagText($text)
	{
		$text = preg_replace('/\b_([^_]+)_\b/', '<span>\\1</span>', $text);
		return $text;
	}

}
