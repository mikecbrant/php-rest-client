<?php

namespace MikeBrant\RestClientLib;

use PHPUnit\Framework\TestCase;

/**
 * Mock for curl_multi_init global function
 * 
 * @return mixed
 */
function curl_multi_init() {
    if (!is_null(RestMultiClientTest::$curlMultiInitResponse)) {
        return RestMultiClientTest::$curlMultiInitResponse;
    }
    return \curl_multi_init();
}

/**
 * Mock for curl_multi_exec global function
 * 
 * @param resource curl_multi handle
 * @param integer flag indicating if there are still active handles.
 * @return integer
 */
function curl_multi_exec($multiCurl, &$active) {
    if (is_null(RestMultiClientTest::$curlMultiExecResponse)) {
        return \curl_multi_exec($multiCurl, $active);
    }
    $active = 0;
    return RestMultiClientTest::$curlMultiExecResponse;
}

/**
 * Mock for curl_multi_getcontent global function
 * 
 * @param resource curl handle
 * @return string
 */
function curl_multi_getcontent($curl) {
    if (!is_null(RestMultiClientTest::$curlMultiGetcontentResponse)) {
        return RestMultiClientTest::$curlMultiGetcontentResponse;
    }
    return \curl_multi_getcontent($curl);
}

/**
 * This is hacky workaround for avoiding double definition of this global method override
 * when running full test suite on this library.
 */
if(!function_exists('\MikeBrant\RestClientLib\curl_getinfo')) {

    /**
     * Mock for curl_getinfo function
     * 
     * @param resource curl handle
     * @return mixed
     */
    function curl_getinfo($curl) {
        $backtrace = debug_backtrace();
        $testClass = $backtrace[1]['class'] . 'Test';
        if (!is_null($testClass::$curlGetinfoResponse)) {
            return $testClass::$curlGetinfoResponse;
        }
        return \curl_getinfo($curl);
    }
}

class RestMultiClientTest extends TestCase{
    public static $curlMultiInitResponse = null;
    
    public static $curlMultiExecResponse = null;
    
    public static $curlMultiGetcontentResponse = null;
    
    public static $curlGetinfoResponse = null;
    
    protected $client = null;
    
    protected $curlMultiExecFailedResponse = CURLM_INTERNAL_ERROR;
    
    protected $curlMultiExecCompleteResponse = CURLM_OK;
    
    protected $curlGetinfoMockResponse = array(
        'url' => 'http://google.com/',
        'content_type' => 'text/html; charset=UTF-8',
        'http_code' => 200,
        'header_size' => 321,
        'request_size' => 49,
        'filetime' => -1,
        'ssl_verify_result' => 0,
        'redirect_count' => 0,
        'total_time' => 1.123264,
        'namelookup_time' => 1.045272,
        'connect_time' => 1.070183,
        'pretransfer_time' => 1.071139,
        'size_upload' => 0,
        'size_download' => 219,
        'speed_download' => 194,
        'speed_upload' => 0,
        'download_content_length' => 219,
        'upload_content_length' => -1,
        'starttransfer_time' => 1.122377,
        'redirect_time' => 0,
        'redirect_url' => 'http://www.google.com/',
        'primary_ip' => '216.58.194.142',
        'certinfo' => array(),
        'primary_port' => 80,
        'local_ip' => '192.168.1.74',
        'local_port' => 59733,
        'request_header' => "GET / HTTP/1.1\nHost: google.com\nAccept: */*",
    );
    
    protected function setUp() {
        self::$curlMultiInitResponse = null;
        self::$curlMultiExecResponse = null;
        self::$curlMultiGetcontentResponse = null;
        self::$curlGetinfoResponse = null;
        $this->client = new RestMultiClient();
    }
    
    protected function tearDown() {
        $this->client = null;
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @covers MikeBrant\RestClientLib\RestMultiClient::validateActionArray
     */
    public function testValidateActionArrayThrowsExceptionOnEmptyArray() {
        $this->client->get(array());
    }
    
    /**
     * @expectedException \LengthException
     * @covers MikeBrant\RestClientLib\RestMultiClient::validateActionArray
     */
    public function testValidateActionArrayThrowsExceptionOnOversizedArray() {
        $maxHandles = $this->client->getMaxHandles();
        $this->client->get(
            array_fill(0, $maxHandles + 1, 'action')
        );
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @covers MikeBrant\RestClientLib\RestMultiClient::validateDataArray
     */
    public function testValidateDataArrayThrowsExceptionOnEmptyArray() {
        $this->client->get(array());
    }
    
    /**
     * @expectedException \LengthException
     * @covers MikeBrant\RestClientLib\RestMultiClient::validateDataArray
     */
    public function testValidateDataArrayThrowsExceptionOnOversizedArray() {
        $maxHandles = $this->client->getMaxHandles();
        $this->client->post(
            array_fill(0, $maxHandles, 'action'),
            array_fill(0, $maxHandles + 1, 'data')
        );
    }
    
    /**
     * @expectedException \Exception
     * @covers MikeBrant\RestClientLib\RestMultiClient::curlMultiSetup
     */
    public function testCurlMultiSetupThrowsExceptionOnCurlMultiInitFailure() {
        self::$curlMultiInitResponse = false;
        $this->client->get(
            array_fill(0, 2, 'action')
        );
    }
    /**
     * @expectedException \Exception
     * @covers MikeBrant\RestClientLib\RestMultiClient::curlMultiExec
     */
    public function testCurlMultiExecThrowsExceptionOnMultiCurlFailure() {
        self::$curlMultiExecResponse = $this->curlMultiExecFailedResponse;
        $this->client->get(
            array_fill(0, 2, 'action')
        );
    }
    
    /**
     * @expectedException \Exception
     * @covers MikeBrant\RestClientLib\RestMultiClient::curlMultiExec
     */
    public function testCurlMultiExecThrowsExceptionOnMalformedCurlHttpResponse() {
        self::$curlMultiExecResponse = $this->curlMultiExecCompleteResponse;
        self::$curlMultiGetcontentResponse = 'test';
        self::$curlGetinfoResponse = array();
        $this->client->get(
            array_fill(0, 2, 'action')
        );
    }
    /**
     * @covers MikeBrant\RestClientLib\RestMultiClient::get
     * @covers MikeBrant\RestClientLib\RestMultiClient::validateActionArray
     * @covers MikeBrant\RestClientLib\RestMultiClient::curlMultiSetup
     * @covers MikeBrant\RestClientLib\RestMultiClient::resetRequestResponseProperties
     * @covers MikeBrant\RestClientLib\RestMultiClient::setRequestUrls
     * @covers MikeBrant\RestClientLib\RestMultiClient::curlMultiExec
     * @covers MikeBrant\RestClientLib\RestMultiClient::curlMultiTeardown
     */
    public function testGet() {
        self::$curlMultiExecResponse = $this->curlMultiExecCompleteResponse;
        self::$curlMultiGetcontentResponse = 'test';
        self::$curlGetinfoResponse = $this->curlGetinfoMockResponse;
        $response = $this->client->get(
            array_fill(0, 2, 'action')
        );
        $this->assertInstanceOf(CurlMultiHttpResponse::class, $response);
        $this->assertAttributeEquals(null, 'curlMultiHandle', $this->client);
    }
    
    /**
     * @expectedException \LengthException
     * @covers MikeBrant\RestClientLib\RestMultiClient::post
     */
    public function testPostThrowsExceptionOnArraySizeMismatch() {
        $maxHandles = $this->client->getMaxHandles();
        $this->client->post(
            array_fill(0, $maxHandles, 'action'),
            array_fill(0, $maxHandles - 1, 'data')
        );
    }
    
    /**
     * @covers MikeBrant\RestClientLib\RestMultiClient::post
     * @covers MikeBrant\RestClientLib\RestMultiClient::validateData
     * @covers MikeBrant\RestClientLib\RestMultiClient::setRequestData
     */
    public function testPost() {
        self::$curlMultiExecResponse = $this->curlMultiExecCompleteResponse;
        self::$curlMultiGetcontentResponse = 'test';
        self::$curlGetinfoResponse = $this->curlGetinfoMockResponse;
        $response = $this->client->post(
            array_fill(0, 2, 'action'),
            array_fill(0, 2, 'data')
        );
        $this->assertInstanceOf(CurlMultiHttpResponse::class, $response);
        $this->assertAttributeEquals(null, 'curlMultiHandle', $this->client);
   }
    
    /**
     * @expectedException \LengthException
     * @covers MikeBrant\RestClientLib\RestMultiClient::put
     */
    public function testPutThrowsExceptionOnArraySizeMismatch() {
        $maxHandles = $this->client->getMaxHandles();
        $this->client->put(
            array_fill(0, $maxHandles, 'action'),
            array_fill(0, $maxHandles - 1, 'data')
        );
    }
    
    /**
     * @covers MikeBrant\RestClientLib\RestMultiClient::put
     */
    public function testPut() {
        self::$curlMultiExecResponse = $this->curlMultiExecCompleteResponse;
        self::$curlMultiGetcontentResponse = 'test';
        self::$curlGetinfoResponse = $this->curlGetinfoMockResponse;
        $response = $this->client->put(
            array_fill(0, 2, 'action'),
            array_fill(0, 2, 'data')
        );
        $this->assertInstanceOf(CurlMultiHttpResponse::class, $response);
        $this->assertAttributeEquals(null, 'curlMultiHandle', $this->client);
    }
    
    /**
     * @covers MikeBrant\RestClientLib\RestMultiClient::delete
     */
    public function testDelete() {
        self::$curlMultiExecResponse = $this->curlMultiExecCompleteResponse;
        self::$curlMultiGetcontentResponse = 'test';
        self::$curlGetinfoResponse = $this->curlGetinfoMockResponse;
        $response = $this->client->delete(
            array_fill(0, 2, 'action')
        );
        $this->assertInstanceOf(CurlMultiHttpResponse::class, $response);
        $this->assertAttributeEquals(null, 'curlMultiHandle', $this->client);
    }
    
    /**
     * @covers MikeBrant\RestClientLib\RestMultiClient::head
     */
    public function testHead() {
        self::$curlMultiExecResponse = $this->curlMultiExecCompleteResponse;
        self::$curlMultiGetcontentResponse = 'test';
        self::$curlGetinfoResponse = $this->curlGetinfoMockResponse;
        $response = $this->client->head(
            array_fill(0, 2, 'action')
        );
        $this->assertInstanceOf(CurlMultiHttpResponse::class, $response);
        $this->assertAttributeEquals(null, 'curlMultiHandle', $this->client);
    }
}