<?php

$payload = Array();

foreach ($notifications as $n) {
	
	$user  = $sso->getUser($n->src->authId);
	
	$payload[] = Array(
		'id'           => $n->_id,
		'url'          => $n->url,
		'media'        => $n->getMediaURI(),
		'content'      => Mention::idToMentions($n->content),
		'timestamp'    => $n->created,
		'timeRelative' => Time::relative($n->created),
		'replies'      => $n->replies->getQuery()->count(),
		'user'         => Array(
			'id'        => $n->src->authId,
			'url'       => strval(url('user', $sso->getUser($n->src->authId)->getUsername())->absolute()),
			'username'  => $user->getUsername(),
			'avatar'    => $user->getAvatar(128),
		)
	);
}

echo json_encode(Array(
	 'payload' => $payload,
	 'until'   => isset($n)? $n->_id : 0
));