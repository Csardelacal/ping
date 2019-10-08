<?php namespace ping\import;

use exceptions\FileNotFoundException;

/**
 * Importers allow Ping to retrieve data from existing JSON files and process
 * it in a meaningful way.
 *
 * This base class provides iteration for the JSON files, so the subclasses only
 * are required to process the data.
 *
 * @author César de la Cal Bretschneider <cesar@magic3w.com>
 */
abstract class Importer
{
	private $filename;
	
	public function __construct($filename) {
		$this->filename = $filename;
	}
			
	/**
	 * When invoked, this method should empty the data target appropriately for the
	 * content of the CSV files that the importer reads.
	 */
	abstract public function purge();
		
	/**
	 * Parses a single entry of a JSON file.
	 *
	 * @param string[] $data The data read from the JSON file
	 */
	abstract public function process($data);
	
	/**
	 *
	 */
	public function run() {
		if (!file_exists($this->filename)) {
			throw new FileNotFoundException(sprintf('File %s could not be found', $this->filename), 1909261106);
		}
		
		$data = json_decode(file_get_contents($this->filename), true);
		$counter = 0;
		
		foreach ($data as $entry) {
			$this->process($entry);
			$counter++;
		}
		
		return $counter;
	}
}
