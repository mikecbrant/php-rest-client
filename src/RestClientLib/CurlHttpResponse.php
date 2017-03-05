<?php

namespace MikeBrant\RestClientLib;

/**
 * @desc Class representing HTTP response as returned from curl call.
 */
class CurlHttpResponse
{
    /**
     * Variable to store response body
     */
    protected $body = null;
    
    /**
     * Variable to store HTTP repsonse code
     * 
     * @var integer
     */
    protected $httpCode = null;
    
    /**
     * Variable to store response content type header
     * 
     * @var string
     */
    protected $contentType = null;
    
    /**
     * Variable to store URL used in request as reported via curl_getinfo().
     * 
     * @var string
     */
    protected $requestUrl = null;
    
    /**
     * Variable to store header used in request as reported via curl_getinfo().
     * 
     * @var string
     */
    protected $requestHeader = null;
    
    /**
     * Variable to store curl getinfo array.
     * See documentation at http://php.net/manual/en/function.curl-getinfo.php for expected array format.
     * 
     * @var array
     */
    protected $curlGetinfo = null;
    
    /**
     * Constructor method.
     * 
     * @param mixed $responseBody Response body as returned from a curl request.
     * @param array $curlGetinto Array returned form curl_getinfo() function call for request.
     * @return void
     * @throws \InvalidArgumentException
     */
    public function __construct($responseBody, array $curlGetinfo) {
        $this->validateGetinfoArray($curlGetinfo);
        $this->body = $responseBody;
        $this->httpCode = $curlGetinfo['http_code'];
        $this->contentType = $curlGetinfo['content_type'];
        $this->requestUrl = $curlGetinfo['url'];
        $this->requestHeader = $curlGetinfo['request_header'];
        $this->curlGetinfo = $curlGetinfo;
    }
    
    /**
     * Returns response body for request
     * 
     * @return mixed
     */
    public function getBody() {
        return $this->body;
    }
    
    /**
     * Returns HTTP response code for request
     * 
     * @return integer
     */
    public function getHttpCode() {
        return $this->httpCode;
    }
    
    /**
     * Returns URL used in request as reported via curl_getinfo().
     * 
     * @return string
     */
    public function getRequestUrl() {
        return $this->requestUrl;
    }
    
    /**
     * Returns header used in request as reported via curl_getinfo().
     * 
     * @return string
     */
    public function getRequestHeader() {
        return $this->requestHeader;
    }
    
    /**
     * Returns curl getinfo array.
     * See documentation at http://php.net/manual/en/function.curl-getinfo.php for expected array format.
     * 
     * @return array
     */
    public function getCurlGetinfo() {
        return $this->curlGetinfo;
    }
    
    /**
     * Method to perform minimal validation of input array as having keys expected to be returned from
     * curl_getinfo().
     * 
     * @throws \InvalidArgumentException
     */
    protected function validateGetinfoArray(array $getinfo) {
        if(empty($getinfo)) {
            throw new \InvalidArgumentException('Empty array passed. Valid curl_getinfo() result array expected.');
        }
        if(!isset($getinfo['http_code']) || !is_integer($getinfo['http_code'])) {
            throw new \InvalidArgumentException('curl_getinfo() response array expects integer value at http_code key.');
        }
        if(!isset($getinfo['content_type']) || !is_string($getinfo['content_type'])) {
            throw new \InvalidArgumentException('curl_getinfo() response array expects string value at content_type key.');
        }
        if(!isset($getinfo['url']) || !is_string($getinfo['url'])) {
            throw new \InvalidArgumentException('curl_getinfo() response array expects string value at url key.');
        }
        if(!isset($getinfo['request_header']) || !is_string($getinfo['request_header'])) {
            throw new \InvalidArgumentException('curl_getinfo() response array expects string value at request_header key.');
        }
    }
}