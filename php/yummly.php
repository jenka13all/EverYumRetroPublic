<?php

/**
 * This script is 'just' proof of concept, that our service to fetch
 * data from yummly works. Well done ;)
 */

$loader = require '../libs/autoload.php';

$config = include __DIR__ . '/../config.php';
$app = new \EverYum\Application($config);

$ingredients = array(
    'pepper',
    'salt',
    'zucchini',
    'mango',
    'garlic',
    'chocolate',
    'marmelade',
    'asparagus',
);
$course = array(
    'course^course-Main Dishes'
);
$diet = array(
    '386^Vegan',
);

$recipes = $app->service['yummly']->getRecipesByIngredients($ingredients, $course, $diet);

echo 'you searched for recipes with ' . implode(', ', $ingredients) . "<br />";
echo 'we found ' . $recipes->totalMatchCount. ' matches (first ' . count($recipes->matches). ' displayed):';
echo '<ul>';

foreach($recipes->matches as $recipe) {
    // get the actual recipe
    $rrecipe = $app->service['yummly']->getRecipe($recipe->id);
    echo '<li>' .
            '<b>' . $recipe->recipeName . '</b><br />' .
            'Score: <b>' . $recipe->rating . '</b><br />' .
            '(<a href="' . $rrecipe->attribution->url . '" target="_blank">' . $rrecipe->attribution->url . '</a>)<br />' .
            '<ul><li>' . implode('</li><li>', $recipe->ingredients) . '</li></ul>' .
         '</li>';
}
echo '</ul>';
