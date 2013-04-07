<?php

require 'libs/tropo.class.php';
$session = new Session();
$to = "+".$session->getParameters("numbertodial");
$msg = $session->getParameters("msg");
$tropo = new Tropo();
$tropo->call($to, array('network'=>'SMS'));
$tropo->say($msg);
return $tropo->RenderJson();

?>
