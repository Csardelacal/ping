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

$router->addRoute('/followers/', ['controller' => 'people', 'action' => 'followingMe']);

/*
 * This is a bit of a hacky route, but allows matching /@csharp to the profile, 
 * as oposed to having to type out /user/csharp
 */
$router->request('', function (Parameters$params, Parameters$server, $extension) {
	$args = array_values($params->getUnparsed());
	
	if (isset($args[1])) { return false; }
	if (!Strings::startsWith($args[0], '@')) { return false; }
	
	return new Path(spitfire(), ['user'], substr($args[0], 1), null);
})
->setReverser(new ClosureReverser(function (Path$path, $explicit = false) {
	$app        = $path->getApp();
	$controller = $path->getController();
	$action     = $path->getAction();
	
	if ($app !== spitfire() || $controller !== ['user']) { return false; }

	return '/@' . $action;
}));