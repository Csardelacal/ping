<?php

use spitfire\core\Environment;

/*
 * Creates a test environment that can be used to store configuration that affects
 * the behavior of an application.
 */
$e = new Environment('test');

// Database
$e->set('db', 'mysqlpdo://www:test@mysql:3306/testdb');
$e->set('db_table_prefix', 'test_');

// oAuth
$e->set('sso', 'http://758701252:DI3ttiLEpFTNCMRpmL61V8jRz6twLQwLXAXcROwFFZP6N8@host.docker.internal:8085');

$e->set('admin_group_id', 1);

//$e->set('db_table_prefix', 'test_');
//$e->set('db_user', 'root');
//$e->set('db_pass', 'root');

//$e->set('sso.endpoint', 'http://host.docker.internal:8085');
//$e->set('sso.appId', '758701252');
//$e->set('sso.appSec', 'DI3ttiLEpFTNCMRpmL61V8jRz6twLQwLXAXcROwFFZP6N8');

$e->set('analytics.id', '');


// Spitfire
$e->set('server_name', 'localhost:8087');
//$e->set('debugging_mode', true);
$e->set('debug_mode', true);

ini_set("memory_limit", "2G");
