<?php

use spitfire\core\Environment;

class Mention
{
	
	public static function mentionsToId($inText) {
		return preg_replace_callback('/(?<=^|\s|\n)\@([a-z0-9_]+)/i', function ($e) {
			$sso  = new auth\SSOCache(Environment::get('sso.endpoint'), Environment::get('sso.appId'), Environment::get('sso.appSec'));
			$user = $sso->getUser($e[1]);
			return '@' . ($user? $user->getId() : $e[1]);
		}, $inText);
	}
	
	public static function idToMentions($inText) {
		return preg_replace_callback('/\@([0-9]+)/i', function ($e) {
			$sso  = new auth\SSOCache(Environment::get('sso.endpoint'), Environment::get('sso.appId'), Environment::get('sso.appSec'));
			$user = $sso->getUser($e[1]);
			
			if (!$user) { return '@' . $e[1]; }
			return sprintf('<a href="%s">@%s</a>', new URL('user', 'show', $user->getUsername()) , $user->getUsername());
		}, $inText);
	}
	
}
