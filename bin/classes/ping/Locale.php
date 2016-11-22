<?php namespace ping;

use spitfire\locale\Locale as ParentClass;

/**
 * This localizer currently doesn't do jack shit.
 */
class Locale extends ParentClass
{
	
	public function getCurrency() {
		return null;
	}

	public function getDateFormatter() {
		return null;
	}

	public function getMessage($msgid) {
		return $msgid;
	}

}
