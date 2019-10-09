<?php namespace ping\import;

use function db;

class CommentsImporter extends Importer
{
	/**
	 * Clean up the database tables.
	 */
	public function purge() {
		//
	}
	

	/**
	 * Processes the data from the "results.json" file
	 * and save it to the databse.
	 *
	 * @param array $data
	 */
	public function process($data) {
		$user = db()->table('user')->get('_id', $data['userID'])->first();
		$author = \AuthorModel::get($user);
		
		$ping = db()->table('ping')
			->get('irt', db()->table('ping')->get('_id', substr($data['socialID'], 3))->first())
			->where('content', substr($data['content'], 0, 500))
			->first();

		if (array_key_exists('responses', $data)) {
			foreach ($data['responses'] as $raw) {
				
				
				$user = db()->table('user')->get('_id', $raw['user'])->first();
				$author = \AuthorModel::get($user);
				
				
				$response = db()->table('ping')
					->get('irt', $ping)
					->where('content', substr($raw['content'], 0, 500))
					->first();
				
				if (!$response) {
					console()->error('Did not find record ')->ln();
					continue;
				}
				
				$response->src = $author;
				$response->deleted = null;
				$response->store();
				
				console()->success('Fixed record ' . $response->_id)->ln();
			}
		}
	}
}
