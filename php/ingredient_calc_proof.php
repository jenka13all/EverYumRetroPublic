<!doctype html>

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=10">
	<meta name="viewport" content="initial-scale=1.0">
	
	<title>EverYumRetro DEV - Ingredient Calculation</title>
	
    <link type="text/css" rel="stylesheet" href="libs/css/styles_v1.css" />
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" language="javascript" type="application/javascript"></script>
</head>

<body>
	
<?php
//vars

$APP_ID = "0660206a";
$APP_KEY = "99ae4898c939cb37f48fbb8fcddd3cdb";
$DIET_TYPE = "allowedDiet[]=386^Vegan";
$MEAL_TYPE = "allowedCourse[]=course^course-Main Dishes";
$MAX_RESULT = "maxResult=500";

$my_items_count = 0;
$my_items = "";

$fridge_list = "corn, peas, carrots, tomato, lentils, tomato paste, tofu, lemon, capers";
$arr_fridge = explode(",", $fridge_list);

$grocery_list = "soy milk, onion, garlic, bannana, lime, cashew, tahini";
$arr_groceries  = explode(", ", $grocery_list);

//don't know if I need this anymore
//$my_list = $fridge_list . ", " . $grocery_list;

$arr_my_items = array_merge($arr_fridge, $arr_groceries);
$my_items_count = count($arr_my_items);

for($i=0; $i<$my_items_count; $i++)
{
	$item = $arr_my_items[$i];
	$my_items .= "&allowedIngredient[]=" . urlencode($item);
	echo $item . "<br>";
}
reset($arr_my_items);
?>
<p>

</p>
<!-- 
	
<p>
dynamic yumly link to get jsonp:
<br>
https://api.yummly.com/v1/api/recipes?_app_id=<?php echo $APP_ID ?>&_app_key=<?php echo $APP_KEY ?>&<?php echo $DIET_TYPE ?>&<?php echo $MEAL_TYPE ?>&facetField[]=ingredient&<?php echo $MAX_RESULT ?><?php echo $my_items ?>

-->

<?php
//decode our json into assoc. array
//json_decode only works with utf-8 encoded data

$json = utf8_encode (file_get_contents("libs/yumly_jsonp.json"));
$json_arr = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);

$arr_recipes = $json_arr["matches"];
$matches  = count($arr_recipes);
?>

<?php
//create & populate our data structure
//$arr_data = (index => array("recipeName", "ingredient string", score))
$arr_data = array();

for($i=0; $i<$matches; $i++)
{	
	$score = 0;
	$recipe_name = $arr_recipes[$i]["recipeName"];
	$ingredients = implode(",", $arr_recipes[$i]["ingredients"]);
	
//score number of matches between my fridge/grocery list and recipe ingredients
	for($n=0; $n<$my_items_count; $n++)
	{
		$item_compare = trim($arr_my_items[$n]);
		if(strpos($ingredients, $item_compare) !== false)
		{
			$score++;
		}
	}
		
	array_push($arr_data, array("recipe_name" => $recipe_name, "ingredients" => $ingredients, "score" => $score));
}

reset($arr_my_items);

//var_dump($json_arr);
?>
<!--
<p>orignal matches  = <?php echo $matches ?>

<p>freaky data structure count = <?php echo count($arr_data); ?></p>
-->
<?php

//sort $arr_data by score - highest first
$arr_scores = array();
foreach ($arr_data as $key => $row)
{
    $scores[$key] = $row['score'];
}

array_multisort($scores, SORT_DESC, $arr_data);

//take only top 5 high scoring recipes
$arr_data = array_slice($arr_data, 0, 5, TRUE);


//we want a "to buy" list now - the $ingredients (in $arr_data) that are NOT in $arr_fridge or in $arr_groceries ($arr_my_items)!
$tobuy = "";
$arr_tobuy = array();

foreach ($arr_data as $key => $value)
{
	echo "recipe = " . $value["recipe_name"] . "<br> score = " . $value["score"] . " ingredient matches <br> ingredient_string = " . $value["ingredients"] . "<p>";
	
	$tobuy = $value["ingredients"];
	
	//for each item in arr_fridge
	//if item in fridge is in string $tobuy ($value["ingredients"]) 
	//delete it from $tobuy
	
//exact match	
	for($n=0; $n<$my_items_count; $n++)
	{
		$item_have = trim($arr_my_items[$n]);

		if(strpos($tobuy, "," . $item_have . ",") !== false)
		{
			echo "item deleted : $item_have<br>";
			$tobuy = str_replace($item_have . ",", "", $tobuy);
		}
	}
	
	reset($arr_my_items);

//close enough that might, might not have to buy - mark	it
	for($n=0; $n<$my_items_count; $n++)
	{
		$item_have = trim($arr_my_items[$n]);

		if(strpos($tobuy, $item_have) !== false)
		{
			echo "item deleted : $item_have<br>";
			$tobuy = str_replace($item_have, "<font color=\"red\">*$item_have*</font>", $tobuy);
		}
 	}
echo "<p>tobuy = $tobuy<p>";
	$arr_tobuy["recipe_name"] = $tobuy;

}
?>



</body>
</html>