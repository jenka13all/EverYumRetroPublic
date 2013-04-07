<?php
$loader = require '../libs/autoload.php';

$config = include __DIR__ . '/../config.php';
$app = new \EverYum\Application($config);

$fridgeGuid = '1e9f19b0-733d-4f0a-ab73-214eb8a28a66';
$recipeNotebookGuid = '0a848310-a119-4503-88c7-d0d9aa443f9b';

$ingredients = $app->service['evernote']->getFridgeContents($fridgeGuid);

echo 'These are the contents of Daans fridge:' .
     '<ul><li>' . implode('</li><li>', $ingredients) . '</li></ul>';

echo '<hr />';


$recipe = array(
    'url' => 'http://www.yummly.com/recipe/White-whole-wheat-pizza-dough-308605',
    'ingredients' => array(
        '4 1/2 cups King Arthur White Whole Wheat Flour',
        '1 3/4 teaspoons salt',
        '1 teaspoon instant yeast',
        '1/4 cup olive oil',
        '1 3/4 cups water, ice cold',
        'a few tablespoons chopped herbs (optional)',
        'Semolina flour or cornmeal for dusting',
    ),
    'images' => array(
        'hostedLargeUrl' => 'http://i.yummly.com/White-whole-wheat-pizza-dough-308605-342729.l.jpg',
        'hostedSmallUrl' => 'http://i.yummly.com/White-whole-wheat-pizza-dough-308605-342729.s.jpg',
    ),
    'name' => 'White Whole Wheat Pizza Dough',
);
echo '<b>Save a recipe to default recipe Notebook:</b>';
$createdNote = $app->service['evernote']->createRecipeNote($recipeNotebookGuid, $recipe);

echo $createdNote->title;