<?php

$payload = Array();

foreach ($notifications as $n) {
	
	$user  = $sso->getUser($n->src->authId);
	
	/*
	 * Get the response data. This is only added if the ping is actually a response
	 * to someone else's message. We can also see here that it would probably make
	 * sense to export this into a separate function within the model.
	 */
	$irt   = $n->irt? [
		'id'           => $n->irt->_id,
		'username'     => $sso->getUser($n->irt->src->authId)->getUsername(),
		'userURL'      => strval(url('user', $sso->getUser($n->irt->src->authId)->getUsername())->absolute()),
		'avatar'       => $sso->getUser($n->irt->src->authId)->getAvatar(32),
		'url'          => $n->irt->deleted? null : $n->irt->url,
		'media'        => $n->irt->deleted? null : $n->irt->getMediaURI(),
		'content'      => $n->irt->deleted? '[Deleted]' : Mention::idToMentions($n->irt->content),
		'timestamp'    => $n->irt->created,
		'timeRelative' => Time::relative($n->irt->created),
		'user'         => Array(
			'id'        => $n->irt->src->authId,
			'url'       => strval(url('user', $sso->getUser($n->irt->src->authId)->getUsername())->absolute()),
			'username'  => $sso->getUser($n->irt->src->authId)->getUsername(),
			'avatar'    => $sso->getUser($n->irt->src->authId)->getAvatar(32)
		)
	] : null;
	
	$payload[] = Array(
		'id'           => $n->_id,
		'url'          => $n->url,
		'media'        => $n->getMediaURI(),
		'content'      => Mention::idToMentions($n->content),
		'timestamp'    => $n->created,
		'timeRelative' => Time::relative($n->created),
		'irt'          => $irt,
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