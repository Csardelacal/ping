<?php

current_context()->response->getHeaders()->contentType('json');

$payload = Array();
$n = null;

foreach ($notifications as $n) {
	
	$user  = $sso->getUser($n->src->user->authId);
	
	
	$poll = db()->table('poll\option')->get('ping', $n->original())->all()->each(function ($e) use ($authUser) {
		$m = new spitfire\cache\MemcachedAdapter();
		
		return [
			'id' => $e->_id,
			'body' => $e->text,
			'responses' => $m->get('responses_' . $e->_id, function () use ($e) { return db()->table('poll\reply')->get('option', $e)->count(); }),
			'selected'  => !!db()->table('poll\reply')->get('option', $e)->where('author', AuthorModel::get(db()->table('user')->get('_id', $authUser->id)->first()))->first()
		];
	});
	
	$payload[] = Array(
		'id'           => $n->_id,
		'url'          => $n->url,
		'media'        => $n->attachmentsPreview(),
		'content'      => Mention::idToMentions($n->content),
		'timestamp'    => $n->created,
		'timeRelative' => Time::relative($n->created),
		'poll'         => $poll->toArray(),
		'feedback'     => [
			'like'      => db()->table('feedback')->get('ping', $n)->where('reaction',  1)->count(),
			'dislike'   => db()->table('feedback')->get('ping', $n)->where('reaction', -1)->count(),
		],
		'shares'       => $n->shared->getQuery()->count(),
		'replies'      => [
			'count'  => $n->replies->getQuery()->count(),
			'sample' => $n->replies->getQuery()->setOrder('created', 'DESC')->range(0, 5)->each(function ($n) use ($sso) {
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
	 'until'   => $n? $n->_id : null,
	 'messages' => spitfire()->getMessages()
));