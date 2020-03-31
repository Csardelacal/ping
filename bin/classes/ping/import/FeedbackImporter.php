<?php namespace ping\import;

use AuthorModel;
use spitfire\exceptions\FileNotFoundException;
use function console;
use function db;

class FeedbackImporter
{
	
	
	private $filename;
	
	public function __construct($filename) {
		$this->filename = $filename;
	}
	
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
		$user = db()->table('user')->get('_id', $data[1])->first();
		$author = AuthorModel::get($user);
		
		$ping = db()->table('ping')->get('_id', $data[2])->first();
		
		if (!$ping) { 
			console()->error('Skipping empty ping')->ln();
			return; 
		}
		
		$feedback = db()->table('feedback')->newRecord();
		$feedback->author = $author;
		$feedback->ping = $ping;
		$feedback->target = $ping->author;
		$feedback->created = $data[3];
		$feedback->appId = $data[0];
		$feedback->reaction = 1;
		$feedback->store();
		
		console()->success('Imported ping...')->ln();
	}
	
	/**
	 *
	 */
	public function run() {
		if (!file_exists($this->filename)) {
			throw new FileNotFoundException(sprintf('File %s could not be found', $this->filename), 1909261106);
		}
		
		$fh = fopen($this->filename, 'r');
		$counter = 0;
		
		while ($entry = fgetcsv($fh)) {
			$this->process($entry);
			$counter++;
		}
		
		return $counter;
	}
}
