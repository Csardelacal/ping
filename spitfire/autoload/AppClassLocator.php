<?php namespace spitfire\autoload;

use Strings;

class AppClassLocator extends ClassLocator
{
	/**
	 * Gets the filename for the expected App. This allows the system to find 
	 * the required file where the class is contained to be included and used.
	 *
	 * @param string $class The complete name of the class
	 */
	public function getFilenameFor($class) {
		if (Strings::endsWith($class, 'App')) {
			$classURI = explode('\\', substr($class, 0, 0 - strlen('app')));
			$filename = 'main';
			$dir = spitfire()->getMapping()->getBaseDir() . 'apps/' . implode(DIRECTORY_SEPARATOR, $classURI);
			return $this->findFile($dir, $filename);
		}
		
		/*
		 * If the class was not found to be an app, then we ignore it and 
		 * continue.
		 */
		return false;
	}

}
