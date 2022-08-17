<?php

current_context()->response->getHeaders()->set('Access-Control-Allow-Origin', '*');
current_context()->response->getHeaders()->set('Access-Control-Allow-Headers', 'Content-Type');

$payload = Array();

foreach ($notifications as $n) {
	
	$user  = $sso->getUser($n->src->user->authId);
	
	
	$poll = db()->table('poll\option')->get('ping', $n->original())->all()->each(function ($e) use ($authUser) {
		$m = new spitfire\cache\MemcachedAdapter();
		
		return [
			'id' => $e->_id,
			'body' => $e->text,
			'url'  => strval(url('poll', 'vote', $e->_id)->absolute()),
			'responses' => $m->get('responses_' . $e->_id, function () use ($e) { return db()->table('poll\reply')->get('option', $e)->count(); }),
			'selected'  => $authUser? !!db()->table('poll\reply')->get('option', $e)->where('author', AuthorModel::get(db()->table('user')->get('_id', $authUser->id)->first()))->first() : false
		];
	});
	
	$payload[] = Array(
		'id'           => $n->_id,
		'url'          => $n->url,
		'media'        => $isModerator || empty($n->removed) ? $n->attachmentsPreview() : '',
		'content'      => $isModerator || empty($n->removed) ? Mention::idToMentions($n->content): '',
		'timestamp'    => $n->created,
		'timeRelative' => Time::relative($n->created),
		'removed'      => $n->removed,
		'staff'        => $isModerator && $n->staff? $sso->getUser($n->staff)->getUsername() : '',
		'poll'         => $poll->toArray(),
		'feedback'     => [
			'mine'      => !!db()->table('feedback')->get('ping', $n)->where('author',  $me)->where('reaction',  1)->first(),
			'like'      => db()->table('feedback')->get('ping', $n)->where('reaction',  1)->count(),
			'dislike'   => db()->table('feedback')->get('ping', $n)->where('reaction', -1)->count(),
		],
		'shares'       => $n->shared->getQuery()->count(),
		'replies'      => [
			'count'  => $n->replies->getQuery()->count(),
			'sample' => $n->replies->getQuery()->where('deleted', NULL)->setOrder('created', 'DESC')->range(0, 5)->each(function ($n) use ($sso, $isModerator) {
				$user  = $sso->getUser($n->src->user->_id);
				return [
					'id'           => $n->_id,
					'url'          => $n->url,
					'media'        => $isModerator || empty($n->removed) ? $n->attachmentsPreview() : '',
					'content'      => $isModerator || empty($n->removed) ? Mention::idToMentions($n->content) : '',
					'timestamp'    => $n->created,
					'timeRelative' => Time::relative($n->created),
					'removed'      => $n->removed,
					'staff'        => $isModerator && $n->staff? $sso->getUser($n->staff)->getUsername() : '',
					'replies'      => [
						'count'  => $n->replies->getQuery()->count()
					],
					'user'         => Array(
						'id'        => $n->src->user->authId,
						'url'       => strval(url('user', $user->getUsername())->absolute()),
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
	 'until'   => isset($n) && $n? $n->_id : null,
	 'messages' => spitfire()->getMessages()
));
