<?php

namespace MikeBrant\RestClientLib;

class CurlMultiHttpResponse
{
    /**
     * Variable to store individual CurlHttpResponse objects from curl_multi call
     * 
     * @var CurlHttpResponse[]
     */
    protected $curlHttpResponses = array();
    
    /**
     * Constructor method. Currently there is no instantiation logic.
     */
    public function __construct() {}
    
    /**
     * Method to add CurlHttpResponse object to collection
     * 
     * @param CurlHttpResponse $response
     * @return void
     */
    public function addResponse(CurlHttpResponse $response) {
        $this->curlHttpResponses[] = $response;
    }
    
    /**
     * Returns array of all CurlHttpResponse objects in collection.
     * 
     * @return CurlHttpResponse[]
     */
    public function getCurlHttpResponses() {
        return $this->curlHttpResponses;
    }
    
    /**
     * Alias for getCurlHttpResponses
     * 
     * @return CurlHttpResponse[]
     */
    public function getAll() {
        return $this->getCurlHttpResponses();
    }
    
    /**
     * Returns array of response bodies for each response in collection.
     * 
     * @return mixed[]
     */
    public function getResponseBodies() {
        return array_map(
            function(CurlHttpResponse $value) {
                return $value->getBody();
            },
            $this->curlHttpResponses
        );
    }
    
    /**
     * Returns array of response codes for each response in collection.
     * 
     * @return integer[]
     */
    public function getHttpCodes() {
        return array_map(
            function(CurlHttpResponse $value) {
                return $value->getHttpCode();
            },
            $this->curlHttpResponses
        );
    }
    
    /**
     * Returns array of URL's used for each response in collectoin as returned via curl_getinfo.
     * 
     * @return string[]
     */
    public function getRequestUrls() {
        return array_map(
            function(CurlHttpResponse $value) {
                return $value->getRequestUrl();
            },
            $this->curlHttpResponses
        );
    }
    
    /**
     * Returns array of request headers for each response in collection as returned via curl_getinfo.
     * 
     * @return string[]
     */
    public function getRequestHeaders() {
        return array_map(
            function(CurlHttpResponse $value) {
                return $value->getRequestHeader();
            },
            $this->curlHttpResponses
        );
    }
    
    /**
     * Returns array of curl_getinfo arrays for each response in collection.
     * See documentation at http://php.net/manual/en/function.curl-getinfo.php for expected format for each array element.
     * 
     * @return array[]
     */
    public function getCurlGetinfoArrays() {
        return array_map(
            function(CurlHttpResponse $value) {
                return $value->getCurlGetinfo();
            },
            $this->curlHttpResponses
        );
    }
}