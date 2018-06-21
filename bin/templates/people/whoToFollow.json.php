<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$payload = [];

$users->each(function ($e) use ($payload, $sso) { 
	$payload[] = [
		'id'       => $e->_id,
		'username' => $sso->getUser($e->_id)->getUsername()
	];	
});

echo json_encode(['result' => 200, 'payload' => $payload]);