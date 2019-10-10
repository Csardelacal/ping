<?php


current_context()->response->getHeaders()->set('Access-Control-Allow-Origin', '*');
current_context()->response->getHeaders()->set('Access-Control-Allow-Headers', 'Content-Type');

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

echo json_encode([
	'id' => $record->_id,
	'secret' => $record->secret,
	'type'   => $record->type
]);