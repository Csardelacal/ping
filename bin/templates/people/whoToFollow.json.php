<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$payload = [];

$users->each(function ($e) use (&$payload, $sso) { 
	
	$user = $sso->getUser($e->_id);
	$mc = new \spitfire\cache\MemcachedAdapter();
	
	try { 
		$banner = $user->getAttribute('banner')->getPreviewURL(320, 75);
		if (!$banner) { throw new Exception(); }
	} 
	catch (Exception$ex) { 
		$banner = 'data:image/svg+xml,%3Csvg%20xmlns=%22http://www.w3.org/2000/svg%22/%3E';
	}
	
	$payload[] = [
		'id'        => $e->_id,
		'username'  => $user->getUsername(),
		'avatar'    => $user->getAvatar(128),
		'banner'    => $banner,
		'followers' => $mc->get('followers_' . $user->getId(), function () use ($user) { return db()->table('follow')->get('prey__id', $user->getId())->count(); })
	];	
});

echo json_encode(['result' => 200, 'payload' => $payload]);