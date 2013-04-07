<?php

//this file sends 3 text messages that correspond to the content of an 
//array called $arr_tobuy. The array's keys are the recipes name. The 
//elements of the array are strings of the required ingredients.
//The script does not return anything.

/*
//setting some data for the array to test it
$ing1 = "apple, banana, potatoes";

$arr_tobuy = array(
'delicious_dish1' => "$ing1",
'delicious_dish2' => "$ing1",
'delicious_dish3' => "$ing1"
);
*/

$keys = array_keys($arr_tobuy);
$key1 = $keys['0'];
$ingredients1 = $arr_tobuy[$key1];

$i=0;

while ($i <= 2)
{
$recipe_name = $keys[$i];
$recipe_missing_elements = $arr_tobuy[$recipe_name];

//figuring out the recipe number
if($i == 0)
{
$rank = "first";
}
elseif($i == 1)
{
$rank = "second";
}
elseif($i == 2)
{
$rank = "third";
}
else
{
$rank = "$i th";
}

//setting the info for the url call
$tropo_text_token = "5873626658576e52546a794d5849666b52444b626f484259536a646d5a51416b51474c42657869516f417a6d";
$number_de = "491752496072";
$msg = "Our $rank suggestion is $recipe_name. All that you still need to cook this recipe is $recipe_missing_elements. Please let us know how you liked the recipe in our facebook group :-)";
$msg_url = urlencode($msg);

$url = "https://tropo.developergarden.com/api/sessions?action=create";
$url .= "&token=";
$url .= $tropo_text_token;
$url .= "&numbertodial=";
$url .= $number_de;
$url .= "&msg=";
$url .= $msg_url;


//curling the url
$curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
                array("Content-type: application/json"));

        $json_response = curl_exec($curl);

        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

//error message
//echo("Call to URL $url completed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));

        curl_close($curl);
        
//text has been sent

$i=$i+1;
}


?>
