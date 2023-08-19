<?php

use commishes\figureSdk\Client;
use spitfire\core\Environment;
use spitfire\provider\Container;

function container()
{
	static $instance;
	
	if (!$instance) {
		$instance = new Container();
	}
	
	return $instance;
}

container()->factory(Client::class, function () {
	return new Client(
		Environment::get('figure.url'),
		Environment::get('figure.token'),
		Environment::get('figure.salt')
	);	
});
