<?php

function return_have_items_array($fridge_list, $grocery_list)
{
//$fridge_list, $grocery_list are simple arrays of strings, separated by commas
//$arr_my_items is a simply array of strings separated by commas

	$arr_fridge = explode(",", $fridge_list);
	$arr_groceries  = explode(", ", $grocery_list);
	$arr_my_items = array_merge($arr_fridge, $arr_groceries);
	
	return $arr_my_items;
}

function return_recipe_array($data)
{
//$data is a file with json data
//$arr_recipes is a json object as assoc. array

	$json = utf8_encode (file_get_contents($data));
	$json_arr = json_decode($json, true, 512, JSON_BIGINT_AS_STRING);
	$arr_recipes = $json_arr["matches"];
	
	return $arr_recipes;
}

function return_shopping_list($arr_my_items, $arr_recipes)
{
//$arr_tobuy is an associative array with string as key (recipe name) and text as value (string items separated by commas)

	$arr_data = array();
	$matches  = count($arr_recipes);
	$my_items_count = count($arr_my_items);

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
	$tobuy_index = 0;

	foreach ($arr_data as $key => $value)
	{
	//echo "recipe = " . $value["recipe_name"] . "<br> score = " . $value["score"] . " ingredient matches <br> ingredient_string = " . $value["ingredients"] . "<p>";
	
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
			//echo "item deleted : $item_have<br>";
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
			//echo "item deleted : $item_have<br>";
				$tobuy = str_replace($item_have, "<font color=\"red\">*$item_have*</font>", $tobuy);
			}
 		}
//echo "<p>tobuy = $tobuy<p>";
		$arr_tobuy[$value["recipe_name"]] = $tobuy;

	}

	return $arr_tobuy;
}
?>