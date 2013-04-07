<?php
$loader = require '../libs/autoload.php';

$config = include __DIR__ . '/../config.php';
$app = new \EverYum\Application($config);

$fridgeId = '1e9f19b0-733d-4f0a-ab73-214eb8a28a66';

$ingredients = $app->service['evernote']->getFridgeContents($fridgeId);

echo 'These are the contents of Daans fridge:' .
     '<ul><li>' . implode('</li><li>', $ingredients) . '</li></ul>';


