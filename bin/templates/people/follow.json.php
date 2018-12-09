<?php

current_context()->response->getHeaders()->set('Access-Control-Allow-Origin', '*');

$data = json_encode(Array(
	 'following' => true
));

echo isset($_GET['p'])? sprintf('%s(%s)', $_GET['p'], $data) : $data;
