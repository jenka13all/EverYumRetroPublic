<?php

// This  is a simple script that calls the MyFrdige note from the user's evernote account
// It takes the content of the list and passes an array $ingredients
// the script is based on the example script for PHP provided by evernote here: https://github.com/evernote/evernote-sdk-php (as of April 6 2013

//   php getNoteFromEvernote.php
//

// Import the classes that we're going to be using
use EDAM\Types\Data, EDAM\Types\Note, EDAM\Types\Resource, EDAM\Types\ResourceAttributes;
use EDAM\Error\EDAMUserException, EDAM\Error\EDAMErrorCode;
use Evernote\Client;

ini_set("include_path", ini_get("include_path") . PATH_SEPARATOR . "../libs/evernote/lib" . PATH_SEPARATOR);

require_once 'autoload.php';

require_once 'Evernote/Client.php';

require_once 'packages/Errors/Errors_types.php';
require_once 'packages/Types/Types_types.php';
require_once 'packages/Limits/Limits_constants.php';

// A global exception handler for our program so that error messages all go to the console
function en_exception_handler($exception)
{
    echo "Uncaught " . get_class($exception) . ":\n";
    if ($exception instanceof EDAMUserException) {
        echo "Error code: " . EDAMErrorCode::$__names[$exception->errorCode] . "\n";
        echo "Parameter: " . $exception->parameter . "\n";
    } elseif ($exception instanceof EDAMSystemException) {
        echo "Error code: " . EDAMErrorCode::$__names[$exception->errorCode] . "\n";
        echo "Message: " . $exception->message . "\n";
    } else {
        echo $exception;
    }
}
set_exception_handler('en_exception_handler');

// Real applications authenticate with Evernote using OAuth, but for the
// purpose of exploring the API, you can get a developer token that allows
// you to access your own Evernote account. To get a developer token, visit
// https://sandbox.evernote.com/api/DeveloperToken.action
// authToken is for the sandbox account for everyummy
$authToken = "S=s1:U=64a04:E=14536500036:C=13dde9ed438:P=1cd:A=en-devtoken:V=2:H=c46e44e1d1ba3853b2f2a4ac1bbc0c2b";

if ($authToken == "your developer token") {
    print "Please fill in your developer token\n";
    print "To get a developer token, visit https://sandbox.evernote.com/api/DeveloperToken.action\n";
    exit(1);
}

// Initial development is performed on our sandbox server. To use the production
// service, change "sandbox.evernote.com" to "www.evernote.com" and replace your
// developer token above with a token from
// https://www.evernote.com/api/DeveloperToken.action
$client = new Client(array('token' => $authToken));

$userStore = $client->getUserStore();

// Connect to the service and check the protocol version
$versionOK =
    $userStore->checkVersion("Evernote EDAMTest (PHP)",
         $GLOBALS['EDAM_UserStore_UserStore_CONSTANTS']['EDAM_VERSION_MAJOR'],
         $GLOBALS['EDAM_UserStore_UserStore_CONSTANTS']['EDAM_VERSION_MINOR']);
print "Is my Evernote API version up to date?  " . $versionOK . "\n\n";
if ($versionOK == 0) {
    exit(1);
}

$noteStore = $client->getNoteStore();

// List all of the notebooks in the user's account
$notebooks = $noteStore->listNotebooks();

//we now have the names of the notebooks

//the element we want to access
$guid ="1e9f19b0-733d-4f0a-ab73-214eb8a28a66";
//getting the content - will return xhtml of the form:<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE en-note SYSTEM "http://xml.evernote.com/pub/enml2.dtd">
<!--<en-note><div>Banana<br clear="none"/>Rice<br clear="none"/>Pepper<br clear="none"/>Salt<br clear="none"/>Olive oil<br clear="none"/>Chili peppers<br clear="none"/>Apple<br clear="none"/>Salami<br clear="none"/>gouda</div></en-note> -->

<?php
$noteContent = $noteStore->getNoteContent($authToken,$guid);

//converting format to string
$noteContentString = (string) $noteContent;
//replacing 

//getting rid of all the extra bits in the xhtml that we don't want
$noteContentString = str_replace('<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE en-note SYSTEM "http://xml.evernote.com/pub/enml2.dtd">','',$noteContentString);
$noteContentString = str_replace('<en-note><div>','',$noteContentString);
$noteContentString = str_replace('</div></en-note>','',$noteContentString);

//ingredients are delivered as an array
$ingredients = explode ('<br clear="none"/>',$noteContentString);

?>