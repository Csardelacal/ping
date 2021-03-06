<?php

use spitfire\core\Environment;

/*
 * Creates a test environment that can be used to store configuration that affects
 * the behavior of an application.
 */
$e = new Environment('test');

$e->set('db_table_prefix', 'test_');
$e->set('db_user', 'root');
$e->set('db_pass', 'root');

$e->set('sso.endpoint', 'http://localhost/Auth');
$e->set('sso.appId', '1545751243');
$e->set('sso.appSec', 'lIvPAQt1dQrc8kVopGcAZCDCUhCUMK0bUmCHS9LduH7iw0');

$e->set('analytics.id', '');