<?php

current_context()->response->getHeaders()->set('Access-Control-Allow-Origin', '*');
current_context()->response->getHeaders()->set('Access-Control-Allow-Headers', 'Content-Type');

echo json_encode(Array(
	'payload' => [
		'id' => $ping->_id,
		'guid' => $ping->guid
	]
));
