<?php

$n = $ping;
$user  = $sso->getUser($n->src->user->_id);

$poll = db()->table('poll\option')->get('ping', $n->original())->all()->each(function ($e) use ($authUser) {
	$m = new spitfire\cache\MemcachedAdapter();
	
	return [
		'id' => $e->_id,
		'body' => $e->text,
		'url'  => strval(url('poll', 'vote', $e->_id)->absolute()),
		'responses' => $m->get('responses_' . $e->_id, function () use ($e) {
			return db()->table('poll\reply')->get('option', $e)->count();
		}),
		'selected'  => $authUser? !!db()->table('poll\reply')->get('option', $e)->where('author', AuthorModel::get(db()->table('user')->get('_id', $authUser->id)->first()))->first() : false
	];
});

$payload = array(
	'id'           => $n->_id,
	'url'          => $n->url,
	'media'        => $isModerator || empty($n->removed) ? $n->attachmentsPreview() : '',
	'content'      => $isModerator || empty($n->removed) ? Mention::idToMentions($n->content) : '',
	'timestamp'    => $n->created,
	'timeRelative' => Time::relative($n->created),
	'removed'      => $n->removed,
	'staff'        => $isModerator ? $sso->getUser($n->staff)->getUsername() : '',
	'poll'         => $poll->toArray(),
	'feedback'     => [
		'mine'      => !!db()->table('feedback')->get('ping', $n)->where('author', $me)->where('reaction', 1)->first(),
		'like'      => db()->table('feedback')->get('ping', $n)->where('reaction', 1)->count(),
		'dislike'   => db()->table('feedback')->get('ping', $n)->where('reaction', -1)->count(),
	],
	'shares'       => $n->shared->getQuery()->count(),
	'replies'      => [
		'count'  => $n->replies->getQuery()->count(),
		'sample' => $n->replies->getQuery()->setOrder('created', 'ASC')->range(0, 5)->each(function ($n) use ($sso, $isModerator) {
			$user  = $sso->getUser($n->src->user->authId);
			return [
				'id'           => $n->_id,
				'url'          => $n->url,
				'media'        => $isModerator || empty($n->removed) ? $n->attachmentsPreview() : '',
				'content'      => $isModerator || empty($n->removed) ? Mention::idToMentions($n->content) : '',
				'timestamp'    => $n->created,
				'timeRelative' => Time::relative($n->created),
				'removed'      => $n->removed,
				'staff'        => $isModerator ? $sso->getUser($n->staff)->getUsername() : '',
				'replies'      => [
					'count'  => $n->replies->getQuery()->count()
				],
				'user'         => array(
					'id'        => $n->src->user->authId,
					'url'       => strval(url('user', $sso->getUser($n->src->user->authId)->getUsername())->absolute()),
					'username'  => $user->getUsername(),
					'avatar'    => $user->getAvatar(128),
				)
			];
		})->toArray()
	],
	'user'         => array(
		'id'        => $n->src->user->authId,
		'url'       => strval(url('user', $sso->getUser($n->src->user->authId)->getUsername())->absolute()),
		'username'  => $user->getUsername(),
		'avatar'    => $user->getAvatar(128),
	)
);

echo json_encode(array(
	 'payload' => $payload,
	 'until'   => isset($n)? $n->_id : 0,
	 'messages' => spitfire()->getMessages()
));
