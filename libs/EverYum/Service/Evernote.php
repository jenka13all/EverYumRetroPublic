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

    protected $token;
    protected $sandbox = false;

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

        $this->token   = $config['evernote.token'];
        $this->sandbox = $config['evernote.sandbox'];

    }

    public function getFridgeContents($id) {

        $client = new Client(array(
            'sandbox' => $this->sandbox,
            'token'   => $this->token,
        ));

        // get the note Store 
        $noteStore = $client->getNoteStore();
        $noteContent = $noteStore->getNoteContent($this->token, $id);

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

        return $ingredients;

    }

}