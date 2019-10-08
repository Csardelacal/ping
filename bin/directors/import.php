<?php

use spitfire\mvc\Director;

class ImportDirector extends Director
{
	
	public function all() {
		$importers = [
			new \ping\import\CommentsImporter('./bin/data/results.json')
		];
		
		foreach ($importers as $importer) {
			console()->info(sprintf('Starting %s importer...', get_class($importer)))->ln();
			console()->info('Deleting old data...')->ln();
			$importer->purge();
			console()->info('Importing new data...')->ln();
			$rows = $importer->run();
			console()->success(sprintf('Imported %s records', $rows))->ln();
		}
	}
	
}
