<?php

use spitfire\cache\MemcachedAdapter;

$payload = array();

foreach ($notifications as $n) {
	$user = $sso->getUser($n->src->user->authId);
	
	/*
	 * Get the response data. This is only added if the ping is actually a response
	 * to someone else's message. We can also see here that it would probably make
	 * sense to export this into a separate function within the model.
	 */
	$irt   = $n->irt? [
		'id'           => $n->irt->_id,
		'username'     => $sso->getUser($n->irt->src->user->authId)->getUsername(),
		'userURL'      => strval(url('user', $sso->getUser($n->irt->src->user->authId)->getUsername())->absolute()),
		'avatar'       => $sso->getUser($n->irt->src->user->authId)->getAvatar(32),
		'url'          => $n->irt->deleted? null : $n->irt->url,
		'media'        => $n->irt->deleted? null : $n->irt->getMediaURI(),
		'content'      => $n->irt->deleted? '[Deleted]' : Mention::idToMentions($n->irt->content),
		'timestamp'    => $n->irt->created,
		'timeRelative' => Time::relative($n->irt->created),
		'user'         => array(
			'id'        => $n->irt->src->user->authId,
			'url'       => strval(url('user', $sso->getUser($n->irt->src->user->authId)->getUsername())->absolute()),
			'username'  => $sso->getUser($n->irt->src->user->authId)->getUsername(),
			'avatar'    => $sso->getUser($n->irt->src->user->authId)->getAvatar(32)
		)
	] : null;
	
	$poll = db()->table('poll\option')->get('ping', $n->original())->all()->each(function ($e) use ($authUser) {
		$m = new spitfire\cache\MemcachedAdapter();
		
		return [
			'id' => $e->_id,
			'body' => $e->text,
			'responses' => $m->get('responses_' . $e->_id, function () use ($e) {
				return db()->table('poll\reply')->get('option', $e)->count();
			}),
			'selected'  => !!db()->table('poll\reply')->get('option', $e)->where('author', AuthorModel::get(db()->table('user')->get('_id', $authUser->id)->first()))->first()
		];
	});
	
	$payload[] = array(
		'id'           => $n->_id,
		'url'          => $n->url,
		'media'        => $isModerator || empty($n->removed) ? $n->attachmentsPreview() : '',
		'content'      => $isModerator || empty($n->removed) ? Mention::idToMentions($n->content) : '',
		'mediaCount'   => $n->attached->getQuery()->count(),
		'explicit'     => !!$n->explicit,
		'timestamp'    => $n->created,
		'timeRelative' => Time::relative($n->created),
		'irt'          => $irt,
		'removed'      => $n->removed,
		'staff'        => $isModerator && $n->staff? $sso->getUser($n->staff)->getUsername() : '',
		'replies'      => $n->replies->getQuery()->count(),
		'feedback'     => [
			'mine'      => !!db()->table('feedback')->get('ping', $n)->where('author', $me)->where('reaction', 1)->first(),
			'like'      => db()->table('feedback')->get('ping', $n)->where('reaction', 1)->count(),
			'dislike'   => db()->table('feedback')->get('ping', $n)->where('reaction', -1)->count(),
		],
		'poll'         => $poll->toArray(),
		'user'         => array(
			'id'        => $n->original()->src->user->authId,
			'url'       => strval(url('user', 'show', $sso->getUser($n->original()->src->user->authId)->getUsername())->absolute()),
			'username'  => $sso->getUser($n->original()->src->user->authId)->getUsername(),
			'avatar'    => $sso->getUser($n->original()->src->user->authId)->getAvatar(128),
		),
		'share'        => $n->share? [
			'id'        => $n->src->user->authId,
			'url'       => strval(url('user', 'show', $sso->getUser($n->src->user->authId)->getUsername())->absolute()),
			'username'  => $user->getUsername(),
			'avatar'    => $user->getAvatar(128)
		] : null
	);
}

$mc = MemcachedAdapter::getInstance();
$stats = $mc->get(sprintf('ping_author_stats_%d', $author->_id), function () use ($author) {
	return [
		'followers' => db()->table('follow')->get('prey', $author->user)->count(),
		'follows'   => db()->table('follow')->get('follower', $author->user)->count(),
		'liked'     => db()->table('feedback')->get('author', $author)->count(),
		'posts'     => db()->table('ping')->get('src', $author->user)->where('target__id', null)->count()
	];
});

current_context()->response->getHeaders()->contentType('json');
echo json_encode(array(
	'@deprecated' => 'These values will be removed in future revisions: payload',
	'payload' => $payload,
	'pings'   => $payload,
	'stats'   => $stats,
	'until'   => isset($n)? $n->_id : 0,
));
