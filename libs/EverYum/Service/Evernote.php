<?php

namespace EverYum\Service;

use EDAM\Types\Data, EDAM\Types\Note, EDAM\Types\Resource, EDAM\Types\ResourceAttributes;
use EDAM\Error\EDAMUserException, EDAM\Error\EDAMErrorCode;
use Evernote\Client;

/**
 * This class is makes use of the Evernote sdk to fetch notes
 * and save new ones
 * 
 *
 * @package EverYum
 * @subpackage Service
 * @author Armin Hackmann
 */
class Evernote extends Service {

    protected $sandbox;

    /**
     * Constructor
     *
     * Please note: We are not calling parent::construct because
     * we will use the Evernote/Client and thus don't need to set
     * it up
     *
     * @access public
     * @param array $config
     * @return void
     */
    public function __construct(array $config) {

        $this->sandbox = $config['evernote.sandbox'];

    }

    public function getClient($token) {

        $client = new Client(array(
            'sandbox' => $this->sandbox,
            'token'   => $token,
        ));

        return $client;

    }

    public function getFridgeContents($token, $guid) {

        $client = $this->getClient($token);

        // get the note Store 
        $noteStore = $client->getNoteStore();
        $noteContent = $noteStore->getNoteContent($token, $guid);

        //converting format to string
        $noteContentString = (string) $noteContent;

        // extract the contents or rather delete everything else (for now)
        // (Maybe this could/should be done with an xml parser?)
        $noteContentString = str_replace("\r\n", '', $noteContentString);
        $noteContentString = str_replace("\n", '', $noteContentString);
        $noteContentString = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $noteContentString);
        $noteContentString = str_replace('<!DOCTYPE en-note SYSTEM "http://xml.evernote.com/pub/enml2.dtd">','',$noteContentString);
        $noteContentString = str_replace('<en-note><div>','',$noteContentString);
        $noteContentString = str_replace('</div></en-note>','',$noteContentString);

        //ingredients are delivered as an array
        $ingredients = explode ('<br clear="none"/>',$noteContentString);

        // List all of the notebooks in the user's account
#        $notebooks = $noteStore->listNotebooks();
#        echo '<pre>'.print_r( $notebooks, true ).'</pre>';exit;

        return $ingredients;

    }

    public function createRecipeNote($token, $notebookGuid, $recipe) {

        $client = $this->getClient($token);
        $noteStore = $client->getNoteStore();

        // TO-DO: Add remote image to Note
        $hashImg = '';
        if (false && $recipe->images[0]->hostedLargeUrl) {
            // To include an attachment such as an image in a note, first create a Resource
            // for the attachment. At a minimum, the Resource contains the binary attachment
            // data, an MD5 hash of the binary data, and the attachment MIME type. It can also
            // include attributes such as filename and location.
            $filename = $recipe->images[0]->hostedLargeUrl;
            $image = fread(fopen($filename, "rb"), filesize($filename));
            $hash = md5($image, 1);

            $data = new Data();
            $data->size = strlen($image);
            $data->bodyHash = $hash;
            $data->body = $image;

            $resource = new Resource();
            $resource->mime = "image/png";
            $resource->data = $data;
            $resource->attributes = new ResourceAttributes();
            $resource->attributes->fileName = $filename;

            // Now, add the new Resource to the note's list of resources
            $note->resources = array( $resource );

            // To display the Resource as part of the note's content, include an <en-media>
            // tag in the note's ENML content. The en-media tag identifies the corresponding
            // Resource using the MD5 hash.
            $hashImg = md5($image, 0);
        }


        $note = new Note();
        $note->notebookGuid = $notebookGuid;
        $note->title = $recipe->name;
        $note->content =
            '<?xml version="1.0" encoding="UTF-8"?>' .
            '<!DOCTYPE en-note SYSTEM "http://xml.evernote.com/pub/enml2.dtd">' .
            '<en-note><b>Ingredients</b><br/> - ' .
            implode('<br /> - ', $recipe->ingredientLines) .
            '<br/><br/>' .
            $recipe->attribution->url .
            (($hashImg)?'<en-media type="image/png" hash="' . $hashImg . '"/>':'') .
            '</en-note>';

        $createdNote = $noteStore->createNote($token, $note);

        return $createdNote;

    }

}