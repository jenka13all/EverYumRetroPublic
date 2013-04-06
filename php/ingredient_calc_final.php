<?php
date_default_timezone_set('UTC');

include_once("../libs/ingredient_calc_functions.php");

$loader = require '../vendor/autoload.php';

$config = include __DIR__ . '/../config.php';
$app = new \EverYum\Application($config);

//$ingredients set here
include_once("getNoteFromEvernote.php");

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