<?php

/**
 * This script is 'just' proof of concept, that our service to send
 * data via Tropo works.
 */

$loader = require '../libs/autoload.php';

$config = include __DIR__ . '/../config.php';
$app = new \EverYum\Application($config);

// fetching user data (theoritcally by caller id)
$user = include __DIR__ . '/../user.php';

$msg = 'Muh';
echo $app->service['tropo']->sendTextMessage($user['cellphone'], $msg);
