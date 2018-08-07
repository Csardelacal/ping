<?php

error_reporting(E_ALL);
ini_set('display_errors', true);
	
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$sem   = msg_get_queue(1337, 0777);
$start = time();

$a = $b = $d = null;

if (php_sapi_name() !== 'cli' || (isset($argv[1]) && $argv[1] === 'rel')) {
	
	echo 'Okay';
	msg_send($sem, 1, time());
}
else {
	
	do {
		
		if(msg_receive($sem, $a, $b, 4096, $d)) {
			echo 'Hello world', PHP_EOL;
			sleep(2);
		}
	}
	while ( time() - 600 < $start);
}