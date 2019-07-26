<?php

use spitfire\core\Path;
use spitfire\core\router\Parameters;
use spitfire\core\router\reverser\ClosureReverser;
use spitfire\core\router\Router;

/* Use this file to add routes to your Spitfire app. Just use them like this
 *
 * router::route('/old/url', '/new/url');
 *
 * Or like this
 * 
 * router:route('old/url/*', 'new/$2/url');
 * 
 * Remember that routes are blocking. If one matches it'll stop the execution
 * of the following rules. So add them wisely.
 * It's really easy and fun!
 */

$router = Router::getInstance();

$router->addRoute('/user/authorize/:token', ['controller' => 'account', 'action' => 'authorize', 'object' => [':token']]);
$router->addRoute('/followers/', ['controller' => 'people', 'action' => 'following']);
$router->addRoute('/following/', ['controller' => 'people', 'action' => 'follows']);


/*
 * This is a bit of a hacky route, but allows matching /@csharp to the profile, 
 * as oposed to having to type out /user/csharp
 */

//$router->request('/user/:username/', ['controller' => ['user'], 'action' => 'show', 'object' => [ ':username' ]]);

$router->request('', function (Parameters$params, Parameters$server, $extension) {
	$args = array_values($params->getUnparsed());
	
	if (isset($args[1])) { return false; }
	if (!Strings::startsWith($args[0], '@')) { return false; }
	
	return new Path(spitfire(), ['user'], 'show', $args[0], $extension);
})
->setReverser(new ClosureReverser(function (Path$path, $explicit = false) {
	$app        = $path->getApp();
	$controller = $path->getController();
	$action     = $path->getAction();
	$object     = $path->getObject();
	
	if ($app !== spitfire()->getMapping()->getURISpace() || $controller !== ['user'] || $action !== 'show') { return false; }

	return '/@' . reset($object);
}));

/*
 * This router is in charge of mapping @user/followers to their followers page, 
 * making it easier to understand where on the page the user currently is.
 * 
 * The reverser should make it simple to disassemble the rule and provide consistent
 * links to the appropriate links anywhere on the site.
 */
$router->addRoute('/:username/followers/', function (Parameters$params, Parameters$server, $extension) {
	if (!Strings::startsWith($params->getParameter('username'), '@')) { return false; }
	return new Path(spitfire(), ['people'], 'following', [$params->getParameter('username')], $extension);
})
->setReverser(new ClosureReverser(function (Path$path, $explicit = false) {
	$app        = $path->getApp();
	$controller = $path->getController();
	$action     = $path->getAction();
	$object     = $path->getObject();
	
	if ($app !== spitfire()->getMapping()->getURISpace() || $controller !== ['people'] || $action !== 'following') { return false; }

	return '/@' . reset($object) . '/followers';
}));


$router->addRoute('/:username/follows/', function (Parameters$params, Parameters$server, $extension) {
	if (!Strings::startsWith($params->getParameter('username'), '@')) { return false; }
	return new Path(spitfire(), ['people'], 'follows', [$params->getParameter('username')], $extension);
})
->setReverser(new ClosureReverser(function (Path$path, $explicit = false) {
	$app        = $path->getApp();
	$controller = $path->getController();
	$action     = $path->getAction();
	$object     = $path->getObject();
	
	if ($app !== spitfire()->getMapping()->getURISpace() || $controller !== ['people'] || $action !== 'follows') { return false; }

	return '/@' . reset($object) . '/follows';
}));