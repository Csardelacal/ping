<?php

use spitfire\core\Environment;

class Mention
{
	
	public static function mentionsToId($inText) {
		return preg_replace_callback('/(?<=^|\s|\n)(\@[a-z0-9_\@]+)/i', function ($e) {
			$user = AuthorModel::find($e[1]);
			$user->store();
			return '@' . ($user? ':' . $user->guid : $e[1]);
		}, $inText);
	}
	
	public static function idToMentions($inText) {
		return preg_replace_callback('/\@(\:?[0-9a-z]+)/i', function ($e) {
			$sso    = new auth\SSOCache(Environment::get('SSO'));
			$author = AuthorModel::find($e[1]);
			$user   = !$author || $author->server? null : $sso->getUser($author->user->_id);
			
			if (!$user) { return '@' . $e[1]; }
			return sprintf('<a href="%s">@%s</a>', url('user', $user->getUsername()) , $user->getUsername());
		}, $inText);
	}
	
	public static function getMentionedUsers($inText) {
		preg_match_all('/\@(\:?[0-9a-z]+)/i', $inText, $matches);
		$matches = $matches[0];
		
		foreach ($matches as &$match) {
			$match = AuthorModel::find(trim($match, '@'));
		}
		
		return array_filter($matches);
	}
	
}

