<?php
namespace EverYum\Service;

/**
 * This is the base class for everyum services
 * (Evernote, DT, Yummly)
 *
 * @package EverYum
 * @subpackage Service
 * @author Armin Hackmann
 */
abstract class Service {

    protected $app;
    protected $baseUri;
    protected $userName;
    protected $password;
    protected $proxy;

    /**
     * Constructor
     *
     * Settings are provided through the 'settings' argument. The following
     * settings are supported:
     *
     *   * baseUri
     *   * userName (optional)
     *   * password (optional)
     *   * proxy (optional)
     *
     * @param \EverYum\Application $app
     * @param array $settings
     */
    public function __construct(\EverYum\Application $app, array $settings) {

        $this->app = $app;

        if (!isset($settings['baseUri'])) {
            throw new \InvalidArgumentException('A baseUri must be provided');
        }

        $validSettings = array(
            'baseUri',
            'userName',
            'password',
            'proxy',
        );

        foreach($validSettings as $validSetting) {
            if (isset($settings[$validSetting])) {
                $this->$validSetting = $settings[$validSetting];
            }
        }

    }

    /**
     * Performs an actual HTTP request, and returns the result.
     *
     * If the specified url is relative, it will be expanded based on the base
     * url.
     *
     * The returned array contains 3 keys:
     *   * body - the response body
     *   * httpCode - a HTTP code (200, 404, etc)
     *   * headers - a list of response http headers. The header names have
     *     been lowercased.
     *
     * @param string $method
     * @param string $url
     * @param string $body
     * @param array $headers
     * @return array
     */
    public function request($method, $url='', $body=null, array $headers=array()) {

        $url = $this->baseUri . $url;

        $curlSettings = array(
            CURLOPT_RETURNTRANSFER => true,
            // Return headers as part of the response
            CURLOPT_HEADER => true,
            CURLOPT_POSTFIELDS => $body,
            // Automatically follow redirects
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
        );

        $curlSettings[CURLOPT_CUSTOMREQUEST] = $method;

        // Adding HTTP headers
        $nHeaders = array();
        foreach($headers as $key=>$value) {

            $nHeaders[] = $key . ': ' . $value;

        }
        $curlSettings[CURLOPT_HTTPHEADER] = $nHeaders;

        if ($this->proxy) {
            $curlSettings[CURLOPT_PROXY] = $this->proxy;
        }

        // initialising curl & doing the request itself
        $curl = curl_init($url);
        curl_setopt_array($curl, $curlSettings);

        $response = curl_exec($curl);
        $curlInfo = curl_getinfo($curl);
        $curlErrNo = curl_errno($curl);
        $curlError = curl_error($curl);

        // dividing the response
        $headerBlob = substr($response, 0, $curlInfo['header_size']);
        $response = substr($response, $curlInfo['header_size']);

        // In the case of 100 Continue, or redirects we'll have multiple lists
        // of headers for each separate HTTP response. We can easily split this
        // because they are separated by \r\n\r\n
        $headerBlob = explode("\r\n\r\n", trim($headerBlob, "\r\n"));

        // We only care about the last set of headers
        $headerBlob = $headerBlob[count($headerBlob)-1];

        // Splitting headers
        $headerBlob = explode("\r\n", $headerBlob);

        $headers = array;
        foreach($headerBlob as $header) {
            $parts = explode(':', $header, 2);
            if (count($parts)==2) {
                $headers[strtolower(trim($parts[0]))] = trim($parts[1]);
            }
        }

        $response = array(
            'body' => $response,
            'statusCode' => $curlInfo['http_code'],
            'headers' => $headers
        );

        if ($curlErrNo) {
            throw new \Exception('[CURL] Error while making request: ' . $curlError . ' (error code: ' . $curlErrNo . ')');
        }

        if ($response['statusCode']>=400) {
            switch ($response['statusCode']) {
                case 400 :
                    throw new \Exception('Bad request', $response['statusCode']);
                case 401 :
                    throw new \Exception('Not authenticated', $response['statusCode']);
                case 402 :
                    throw new \Exception('Payment required', $response['statusCode']);
                case 403 :
                    throw new \Exception('Forbidden', $response['statusCode']);
                case 404:
                    throw new \Exception('Resource not found', $response['statusCode']);
                case 405 :
                    throw new \Exception('Method not allowed', $response['statusCode']);
                case 409 :
                    throw new \Exception('Conflict', $response['statusCode']);
                case 412 :
                    throw new \Exception('Precondition failed', $response['statusCode']);
                case 416 :
                    throw new \Exception('Requested Range Not Satisfiable', $response['statusCode']);
                case 500 :
                    throw new \Exception('Internal server error', $response['statusCode']);
                case 501 :
                    throw new \Exception('Not Implemented', $response['statusCode']);
                case 507 :
                    throw new \Exception('Insufficient storage', $response['statusCode']);
                default:
                    throw new \Exception('HTTP error response', $response['statusCode']);
            }
        }

        return $response;

    }

}
