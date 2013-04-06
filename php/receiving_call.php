<?php

require '../libs/tropo.class.php';
require '../libs/limonade-master/lib/limonade.php';
dispatch('/', 'hello');
function hello()
{
    return 'Hello world!';
}
dispatch_post('/start', 'app_start');
function app_start() {
    $tropo = new Tropo();
    $options = array("choices" => "Chinese, Mexican, Indian, Colombian, German, Italian, French", "name" => "foodstyle", "timeout" => 15);
    $tropo->ask("Hi! Welcome to Ever yum! What kind of dish do you want to eat tonight?", $options);
    $tropo->on(array("event" => "continue", "next" => "http://www.google.de?uri=continue"));
    $tropo->RenderJson();
}
dispatch_post('/continue', 'app_continue');
function app_continue() {
    $tropo = new Tropo();
    @$result = new Result();
    $answer = $result->getValue();
    $tropo->say("You said " . $answer);
    $tropo->RenderJson();
}
run();

/*
require 'tropo.class.php';
require 'limonade-master/lib/limonade.php';
//limonade hinzufügen

$tropo = new Tropo();
$tropo->ask('Hi! Welcome to Ever yum! What kind of dish do you want to eat tonight?', array(
  'choices'=>'Chinese, Mexican, Indian, Colombian, German, Italian, French',
  'event'=> array(
    'nomatch' => 'Never heard of it.',
    'timeout' => 'Speak up!',
    )
  ));
// Tell Tropo how to continue if a successful choice was made
$tropo->ask('Fantastic! I love that, too! Do you prefer any dietary enhancements like vegan or gluten free?', array(
  'choices'=>'vegan, vegetarian, gluten free',
  'event'=> array(
    'nomatch' => 'Never heard of it.',
    'timeout' => 'Speak up!',
    )
  ));
$tropo->on(array('event' => 'continue', 'say'=> 'Sounds perfect! You will receive a text message with three cooking suggestions based on the content of your fridge in the next 2 minutes.'));
// Render the JSON back to Tropo    
$tropo->renderJSON();
*/
?>
