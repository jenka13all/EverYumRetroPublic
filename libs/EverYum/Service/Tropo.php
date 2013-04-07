<?php

namespace EverYum\Service;

/**
 * This class is responsible for handling Requests to Yummly
 * (http://www.yummly.com).
 *
 * @package EverYum
 * @subpackage Service
 * @author Daan Löning & Armin Hackmann
 */
class Tropo extends Service {

    protected $textToken;

    /**
     * __construct
     *
     * Constructing the Service and setting the Tropo-specific
     * configuration
     *
     * @access public
     * @param array $config
     * @return void
     */
    public function __construct(array $config) {

        parent::__construct(array(
            'baseUri' => $config['tropo.endpoint'],
            'proxy'   => isset($config['proxy'])?$config['proxy']:null,
        ));

        $this->textToken = $config['tropo.textToken'];

    }

    public function sendTextMessage($cellphone, $msg) {

        $url = 'sessions';
        $parameters = array(
            'action'       => 'create',
            'token'        => $this->textToken,
            'numbertodial' => $cellphone,
            'msg'          => $msg,
        );

        $result = $this->request('GET', $url . '?' . http_build_query($parameters), '', array(
            "Content-type:" => "application/json",
        ));
        echo '<pre>'.print_r($result, true) . '</pre>';

    }

}
