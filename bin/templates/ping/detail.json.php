<?php

current_context()->response->getHeaders()->set('Access-Control-Allow-Origin', '*');
current_context()->response->getHeaders()->set('Access-Control-Allow-Headers', 'Content-type');
current_context()->response->getHeaders()->contentType('json');

$n = $ping;
$user  = $sso->getUser($n->src->user->_id);
	
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


$mc = new \spitfire\cache\MemcachedAdapter;
$mc->setTimeout(60);
$myreaction = db()->table('feedback')->get('ping', $n)->where('author',  $me)->first();
$feedback = $mc->get('ping_like_details_' . $ping->_id, function () use ($ping) {
	$reactions = ping\Reaction::all();
	$_ret = [
		'count' => []
	];

	foreach ($reactions as $reaction) {
		$_ret['count'][$reaction->getIdentifier()] = db()->table('feedback')->get('ping', $ping)->where('reaction',  $reaction->getIdentifier())->where('removed', null)->count();
	}

	$_ret['sample'] = db()->table('feedback')->get('ping', $ping)->where('removed', null)->range(0, 10)->each(function ($e) { return [
		'author' => $e->author->_id,
		'reaction' => $e->reaction,
		'user' => $e->author->user? $e->author->user->_id : null, 
		'avatar' => $e->author->getAvatar(), 
		'username' => $e->author->getUsername(), 
	];})->toArray();

	return $_ret;
});

$feedback['mine'] = $myreaction? $myreaction->reaction : null;

$payload = Array(
	'id'           => $n->_id,
	'url'          => $n->url,
	'media'        => $n->attachmentsPreview(),
	'content'      => Mention::idToMentions($n->content),
	'timestamp'    => $n->created,
	'timeRelative' => Time::relative($n->created),
	'poll'         => $poll->toArray(),
	'feedback'     => $feedback,
	'shares'       => $n->shared->getQuery()->count(),
	'embed'        => collect($ping->embed->toArray())->each(function ($e) {
			return ['short' => $e->short, 'extended' => $e->url, 'title' => $e->title, 'description' => $e->description, 'image' => $e->image];
		})->toArray(),
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

echo json_encode(Array(
	 'payload' => $payload,
	 'until'   => isset($n)? $n->_id : 0,
	 'messages' => spitfire()->getMessages()
));