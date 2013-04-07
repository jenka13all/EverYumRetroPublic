<?php
include_once("../libs/ingredient_calc_functions.php");

$loader = require '../libs/autoload.php';

$config = include __DIR__ . '/../config.php';
$app = new \EverYum\Application($config);

// fetching user data (theoritcally by caller id)
$user = include __DIR__ . '/../user.php';

// TO-DO: replace with Tropo values
$course = array('course^course-Main Dishes');
$diet = array('386^Vegan');

// fetch fridge contents from Evernote
$fridge_list = $app->service['evernote']->getFridgeContents($user['evernote.token'], $user['evernote.fridgeNoteGuid']);
$grocery_list = array("soy milk");
$arr_my_items = array_merge($grocery_list, $fridge_list);

// fetch possible recipes from Yummly
$arr_recipes = $app->service['yummly']->getRecipesByIngredients($arr_my_items, $course, $diet);

// calculate best matches
$bestMatches = $app->getBestMatches($arr_my_items, $arr_recipes);

// iterate over the best matches and
$i=0;
foreach($bestMatches as $recipe) {
    switch($i){
        case 0:
            $rank = 'first';
            break;
        case 1:
            $rank = 'second';
            break;
        case 2:
            $rank = 'third';
            break;
        default:
            $rank = ($i+1) . 'th';
            break;
    }

    // get recipe
    $detail = $app->service['yummly']->getRecipe($recipe['id']);

    // save to Evernote
    $app->service['evernote']->createRecipeNote($user['evernote.token'], $user['evernote.recipeNotebookGuid'], $detail);

    // send text message
    $message = 'Our ' . $rank . ' suggestion is "' . $recipe['name'] . '". All that you still need to cook this recipe is: ' . $recipe['toBuy'] . '. Please let us know how you liked the recipe in our facebook group :-)';

    echo $message.'<br /><br />';
    $app->service['tropo']->sendTextMessage($user['cellphone'], $message);

    break;
    $i++;
}
