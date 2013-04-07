<?php
$loader = require '../libs/autoload.php';

$config = include __DIR__ . '/../config.php';
$app = new \EverYum\Application($config);

// fetching user data (theoritcally by caller id)
$user = include __DIR__ . '/../user.php';

$ingredients = $app->service['evernote']->getFridgeContents($user['evernote.token'], $user['evernote.fridgeNoteGuid']);

echo 'These are the contents of Daans fridge:' .
     '<ul><li>' . implode('</li><li>', $ingredients) . '</li></ul>';

echo '<hr />';


$recipe = $app->service['yummly']->getRecipe('White-whole-wheat-pizza-dough-308605');

echo '<b>Save a recipe to default recipe Notebook:</b>';
#$createdNote = $app->service['evernote']->createRecipeNote($user['evernote.token'], $user['evernote.recipeNotebookGuid'], $recipe);
#echo $createdNote->title;

echo 'We don\'t want to spam Daans Recipe Notebook, so I\'m disabling this...';
