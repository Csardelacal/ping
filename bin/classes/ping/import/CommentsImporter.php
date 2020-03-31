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
		try {
			$user = db()->table('user')->get('_id', $data['userID'])->first();
			$author = \AuthorModel::get($user);

			$ping = db()->table('ping')->newRecord();
			$ping->src = $author;
			$ping->irt = db()->table('ping')->get('_id', substr($data['socialID'], 3))->first();
			$ping->content = substr($data['content'], 0, 500);
			$ping->created = $data['created'];
			$ping->store();

			if (array_key_exists('responses', $data)) {
				foreach ($data['responses'] as $raw) {

					$ruser = db()->table('user')->get('_id', $raw['user'])->first();
					$rauthor = \AuthorModel::get($ruser);

					$response = db()->table('ping')->newRecord();
					$response->irt = $ping;
					$response->content = substr($raw['content'], 0, 500);
					$response->created = $raw['created'];
					$response->src = $rauthor;
					$response->deleted = null;
					$response->store();

					console()->success('Inserted record ' . $response->_id)->ln();
				}
			}
		}
		catch (\Exception$e) {
			console()->error('Skipped a record')->ln();
		}
	}
}
