<?php

$payload = Array();

foreach ($notifications as $n) {
	
	$user  = $sso->getUser($n->src->authId);
	
	$payload[] = Array(
		'id'           => $n->_id,
		'url'          => $n->url,
		'type'         => $n->type,
		'content'      => Mention::idToMentions($n->content),
		'timestamp'    => $n->created,
		'timeRelative' => Time::relative($n->created),
		'user'         => Array(
			'id'        => $n->src->authId,
			'username'  => $user->getUsername(),
			'avatar'    => $user->getAvatar(128),
		)
	);
}

echo json_encode(Array(
	 'payload' => $payload,
	 'until'   => isset($n)? $n->_id : 0
));