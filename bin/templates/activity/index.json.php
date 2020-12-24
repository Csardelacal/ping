<?php

$payload = Array();

foreach ($notifications as $n) {
	
	if ($n->src) {
		try { $user = $sso->getUser($n->src->user->_id); }
		catch (\Exception$e) { $user = null; }
	}
	else {
		$user = null;
	}
	
	$payload[] = Array(
		'id'           => $n->_id,
		'url'          => $n->url,
		'type'         => $n->type,
		'content'      => Mention::idToMentions($n->content),
		'timestamp'    => $n->created,
		'timeRelative' => Time::relative($n->created),
		'user'         => Array(
			'id'        => $n->src? $n->src->user->_id : null,
			'username'  => $user? $user->getUsername() : null,
			'display'   => $user? $user->getUsername() : 'Someone',
			'avatar'    => $user? $user->getAvatar(128) : \spitfire\core\http\URL::asset($asset_name),
		)
	);
}

echo json_encode(Array(
	 'payload' => $payload,
	 'until'   => isset($n)? $n->_id : 0
));