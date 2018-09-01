<?php

use spitfire\core\Environment;

/*
 * Creates a test environment that can be used to store configuration that affects
 * the behavior of an application.
 */
$e = new Environment('test');

$e->set('db', 'mysqlpdo://root:root@localhost/ping?encoding=utf8&prefix=p_');
$e->set('SSO',  'http://1545751243:lIvPAQt1dQrc8kVopGcAZCDCUhCUMK0bUmCHS9LduH7iw0@localhost/Auth/');

$e->set('analytics.id', '');
$e->set('debug_mode', 1);
$e->set('debugging_mode', $e->get('debug_mode'));