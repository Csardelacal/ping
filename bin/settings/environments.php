<?php

use spitfire\core\Environment;

/*
 * Creates a test environment that can be used to store configuration that affects
 * the behavior of an application.
 */
$e = new Environment('test');

$e->set('db', 'mysqlpdo://root:@localhost/ping?encoding=utf8&prefix=p_');
$e->set('SSO',  'http://1638362706:sAGJBqe342XawmQBuaaNDo7ybKUj257CtWQRMwYbm2bMq9I@localhost/PHPAuthServer/');

$e->set('analytics.id', '');
$e->set('debug_mode', 1);
$e->set('debugging_mode', $e->get('debug_mode'));