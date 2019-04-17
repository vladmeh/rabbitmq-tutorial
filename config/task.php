<?php

require_once __DIR__ . '/../vendor/autoload.php';

define('HOST', '192.168.40.67'); //192.168.36.90
define('PORT', 5672);
define('USER', 'test');
define('PASS', 'test');
define('VHOST', '/');

//If this is enabled you can see AMQP output on the CLI
define('AMQP_DEBUG', false);