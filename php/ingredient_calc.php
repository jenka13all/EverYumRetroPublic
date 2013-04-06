<!doctype html>

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=10">
	<meta name="viewport" content="initial-scale=1.0">
	
	<title>EverYumRetro DEV - Ingredient Calculation</title>
	
    <link type="text/css" rel="stylesheet" href="css/styles_v1.css" />
 </head>

<body>
	
<?php
include_once("../libs/ingredient_calc_functions.php");

//replace with EverNote results
$fridge_list = "corn, peas, carrots, tomato, lentils, tomato paste, tofu, lemon, capers";
$grocery_list = "soy milk, onion, garlic, bannana, lime, cashew, tahini";

$arr_my_items = return_have_items_array($fridge_list, $grocery_list);
$my_items_count = count($arr_my_items);
?>

in my fridge / on my grocery list:
<p></p>

<?php
//build yumly URL
for($i=0; $i<$my_items_count; $i++)
{
	$item = $arr_my_items[$i];
	echo $item . "<br>";
}
reset($arr_my_items);
?>

<p>
What to buy in order to make...
</p>


<?php
//replace filename with json data variable
$arr_recipes = return_recipe_array("../libs/yumly_jsonp.json");

?>

<?php
$arr_tobuy = return_shopping_list($arr_my_items, $arr_recipes);

//show what it can do
foreach ($arr_tobuy as $recipe => $shopping_list)
{
?>

	<b><?php echo $recipe ?>:</b><br>
	<?php echo str_replace(",", "<br>", $shopping_list); ?>
	<p></p>	

<?php
}
?>

</body>
</html>
