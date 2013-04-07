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

#echo 'As we don\'t know how may text messages can be sent in the sandbox account, I\'m disabling the proof-of-concept for now :)';
