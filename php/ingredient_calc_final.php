<?php
date_default_timezone_set('UTC');

include_once("../libs/ingredient_calc_functions.php");

$loader = require '../libs/autoload.php';

$config = include __DIR__ . '/../config.php';
$app = new \EverYum\Application($config);

// fetching user data (theoritcally by caller id)
$user = include __DIR__ . '/../user.php';

// fetch fridge contents from Evernote
$app->service['evernote']->getFridgeContents($user['evernote.token'], $user['evernote.fridgeNoteGuid']);

//replace with Tropo values
$course = array('course^course-Main Dishes');
$diet = array('386^Vegan');

//replace with EverNote results
$grocery_list = "soy milk";
$fridge_list = implode(",", $ingredients);

$arr_my_items = return_have_items_array($fridge_list, $grocery_list);
$my_items_count = count($arr_my_items);

$arr_recipes = $app->service['yummly']->getRecipesByIngredients($ingredients, $course, $diet);

$arr_tobuy = return_shopping_list_obj($arr_my_items, $arr_recipes);

//foreach ($arr_tobuy as $recipe => $shopping_list)
?>