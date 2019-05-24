<?php

$payload = Array();

foreach ($notifications as $n) {
	
	$user  = $sso->getUser($n->src->user->authId);
	
	$payload[] = Array(
		'id'           => $n->_id,
		'url'          => $n->url,
		'media'        => $n->attachmentsPreview(),
		'content'      => Mention::idToMentions($n->content),
		'timestamp'    => $n->created,
		'timeRelative' => Time::relative($n->created),
		'replies'      => [
			'count'  => $n->replies->getQuery()->count(),
			'sample' => $n->replies->getQuery()->setOrder('created', 'ASC')->range(0, 5)->each(function ($n) use ($sso) {
				$user  = $sso->getUser($n->src->user->authId);
				return [
					'id'           => $n->_id,
					'url'          => $n->url,
					'media'        => $n->attachmentsPreview(),
					'content'      => Mention::idToMentions($n->content),
					'timestamp'    => $n->created,
					'timeRelative' => Time::relative($n->created),
					'replies'      => [
						'count'  => $n->replies->getQuery()->count()
					],
					'user'         => Array(
						'id'        => $n->src->user->authId,
						'url'       => strval(url('user', $sso->getUser($n->src->user->authId)->getUsername())->absolute()),
						'username'  => $user->getUsername(),
						'avatar'    => $user->getAvatar(128),
					)
				];
			})->toArray()
		],
		'user'         => Array(
			'id'        => $n->src->user->authId,
			'url'       => strval(url('user', $sso->getUser($n->src->user->authId)->getUsername())->absolute()),
			'username'  => $user->getUsername(),
			'avatar'    => $user->getAvatar(128),
		)
	);
}

echo json_encode(Array(
	 'payload' => $payload,
	 'messages' => spitfire()->getMessages()
));