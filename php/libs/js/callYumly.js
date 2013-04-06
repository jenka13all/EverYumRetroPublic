
function callYumly(ingredients)
{

	var container = $('#_recipes');
	var psearch = "";
	
	var APP_ID = "0660206a";
	var APP_KEY = "99ae4898c939cb37f48fbb8fcddd3cdb";
	var DIET_TYPE = "allowedDiet[]=386^Vegan";
	var MEAL_TYPE = "allowedCourse[]=course^course-Main Dishes";
	var MAX_RESULT = 5;

//ingredients is a comma-separated string
	arr_ingredients = ingredients.split(",");

	for(var i = 0; i < arr_ingredients.length; i++)
	{
		ingredient = arr_ingredients[i];
		psearch += "&allowedIngredient[]=" + encodeURIComponent(ingredient);
	}

	if(ingredients == "")
	{
	 	var err = "Enter somethin'... or you'll get NOTHIN'!";
	 	container.text(err);
	} else {
	
//url has to have same type of http - or https - as the server calling it!	
		var url = "https://api.yummly.com/v1/api/recipes?_app_id=" + APP_ID + "&_app_key=" + APP_KEY + 
		"" + psearch + "&" + DIET_TYPE + "&" + MAX_RESULT + "&" + MEAL_TYPE + "&facetField[]=ingredient";
		
//call ajax method on url with jsonpCallback method -- call this method on success, define method below

		console.log(url);

		$.ajax({
    		url: url,
    		type: 'GET',
    		jsonpCallback: 'parseYumly',
    		dataType: "jsonp",
    		success: function(data) {
        		parseYumly(data);
    		}
		});
    
 	}

}

function parseYumly(data)
{
    var container = $('#_recipes');
    
    var credit = data.attribution.html;
    var arr_recipes = data.matches;
    
    var search_ingredients = jQuery('#_ingredients').val();
    var arr_search_ingredients = search_ingredients.split(",");
    
//algorithm to rank results by number of matching searched ingredients - return the top 3    
    
	container.html(credit + "!<p>");
	
	for(var i = 0; i < arr_recipes.length; i++)
	{
		recipe = arr_recipes[i];
		arr_ingredients = recipe.ingredients;
		
		container.append("<span style=\"font-weight: bold;\">" + recipe.recipeName + "</span><br>");
		container.append("<ul>");
		
		for(var n=0; n<arr_ingredients.length; n++)
		{
			ingredient = arr_ingredients[n];
			
			if($.inArray(ingredient, arr_search_ingredients) > -1)
			{
			  container.append("<li style=\"color: red;\">" + ingredient + "</li>");			
			} else {
			
			  container.append("<li>" + ingredient + "</li>");
			}
		}
		
		container.append("</ul><p>");
	}
}
