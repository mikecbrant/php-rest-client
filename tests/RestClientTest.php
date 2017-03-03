<?php

namespace MikeBrant\RestClientLib;

use MikeBrant\RestClientLib\RestClient;
use PHPUnit\Framework\TestCase;

/**
 * Mock for curl_init global function
 * 
 * @return mixed
 */
function curl_init() {
    if (!is_null(RestClientTest::$curlInitResponse)) {
        return RestClientTest::$curlInitResponse;
    }
    return \curl_init();
}

/**
 * Mock for curl_exec global function
 * 
 * @return mixed
 */
function curl_exec($curl) {
    if (!is_null(RestClientTest::$curlExecResponse)) {
        return RestClientTest::$curlExecResponse;
    }
    return \curl_exec($curl);
}

/**
 * Mock for curl_error global function
 * 
 * @return mixed
 */
function curl_error($curl) {
    if (!is_null(RestClientTest::$curlErrorResponse)) {
        return RestClientTest::$curlErrorResponse;
    }
    return \curl_error($curl);
}

/**
 * Mock for curl_getinfo function
 * 
 * @return mixed
 */
function curl_getinfo($curl) {
    if (!is_null(RestClientTest::$curlGetInfoResponse)) {
        return RestClientTest::$curlGetInfoResponse;
    }
    return \curl_getinfo($curl);
}

class RestClientTest extends TestCase{
    protected $client = null;
    
    protected $curlExecMockResponse = 'Test Response';
    
    protected $curlGetInfoMockResponse = array(
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
    
    public static $curlInitResponse = null;
    
    public static $curlExecResponse = null;
    
    public static $curlErrorResponse = null;
    
    public static $curlGetInfoResponse = null;
    
    protected function setUp() {
        self::$curlInitResponse = null;
        self::$curlExecResponse = null;
        self::$curlErrorResponse = null;
        self::$curlGetInfoResponse = null;
        $this->client = new RestClient();
    }
    
    protected function tearDown() {
        $this->client = null;
    }
    
    public function notStringProvider() {
        return array(
            array(null),
            array(new \stdClass()),
            array(1),
            array(0),
            array(true),
            array(false),
            array(array())
        );
    }
    
    public function emptyProvider() {
        return array(
            array(null),
            array(''),
            array(0),
            array(0.0),
            array(false),
            array('0'),
            array(array())
        );
    }
    
    public function notStringAndEmptyProvider() {
        return array(
            array(null),
            array(''),
            array(new \stdClass()),
            array(1),
            array(0),
            array(0.0),
            array('0'),
            array(true),
            array(false),
            array(array())
        );
    }
    
    public function hostProvider() {
        return array(
            array('somedomain.com', 'somedomain.com', false),
            array('somedomain.com/', 'somedomain.com', false),
            array('https://somedomain.com', 'somedomain.com', true),
            array('http://somedomain.com', 'somedomain.com', false),
            array('somedomain.com:80', 'somedomain.com:80', false),
            array('somedomain.com:443', 'somedomain.com:443', true),
            array('somedomain.com:8443', 'somedomain.com:8443', true)
        );
    }
    
    public function notBooleanProvider() {
        return array(
            array(null),
            array(''),
            array('string'),
            array('true'),
            array('false'),
            array(1),
            array(0),
            array('1'),
            array('0'),
            array(0.0),
            array(new \stdClass()),
            array(array())
        );
    }
    
    public function uriBaseProvider() {
        return array(
            array('test', '/test/'),
            array('/test', '/test/'),
            array('test/', '/test/'),
            array('/test/', '/test/')
        );
    }
    
    public function notZeroOrPositiveIntegerProvider() {
        return array(
            array(-1),
            array(null),
            array(''),
            array(new \stdClass()),
            array(1.0),
            array('1'),
            array(array())
        );
    }
    
    public function headersProvider() {
        return array(
            array(
                array(
                    'header1' => 'header1 value',
                    'header2' => 'header2 value'
                )
            )
        );
    }
    
    public function buildUriProvider() {
        return array(
            array(true, 'google.com', 'base', 'action', 'https://google.com/base/action'),
            array(false, 'google.com', 'base', 'action', 'http://google.com/base/action')
        );
    }
    
    /**
     * @dataProvider notStringProvider
     * @expectedException \InvalidArgumentException
     * @covers MikeBrant\RestClientLib\RestClient::validateAction
     */
    public function testValidateActionThrowsExceptions($action) {
        $this->client->get($action);
    }
    
    /**
     * @dataProvider emptyProvider
     * @expectedException \InvalidArgumentException
     * @covers MikeBrant\RestClientLib\RestClient::validateData
     */
    public function testValidateDataThrowsExceptions($data) {
        $this->client->post('', $data);
    }
    
    /**
     * @dataProvider notStringAndEmptyProvider
     * @expectedException \InvalidArgumentException
     * @covers MikeBrant\RestClientLib\RestClient::setRemoteHost
     */
    public function testSetRemoteHostThrowsExceptions($host) {
        $this->client->setRemoteHost($host);
    }
    
    /**
     * @dataProvider hostProvider
     * @covers MikeBrant\RestClientLib\RestClient::setRemoteHost
     * @covers MikeBrant\RestClientLib\RestClient::getRemoteHost
     */
    public function testSetRemoteHost($hostInput, $hostOutput, $useSslSet) {
        $this->client->setRemoteHost($hostInput);
        $this->assertEquals($hostOutput, $this->client->getRemoteHost());
        $this->assertEquals($useSslSet, $this->client->isUsingSsl());
    }
    
    /**
     * @dataProvider notStringAndEmptyProvider
     * @expectedException \InvalidArgumentException
     * @covers MikeBrant\RestClientLib\RestClient::setUriBase
     */
    public function testSetUriBaseThrowsExceptions($string) {
        $this->client->setUriBase($string);
    }
    
    /**
     * @dataProvider uriBaseProvider
     * @covers MikeBrant\RestClientLib\RestClient::setUriBase
     * @covers MikeBrant\RestClientLib\RestClient::getUriBase
     */
    public function testSetUriBase($stringInput, $stringOutput) {
        $this->client->setUriBase($stringInput);
        $this->assertEquals($stringOutput, $this->client->getUriBase());
    }
    
    /**
     * @dataProvider notBooleanProvider
     * @expectedException \InvalidArgumentException
     * @covers MikeBrant\RestClientLib\RestClient::setUseSsl
     */
    public function testSetUseSslThrowsExceptions($boolean) {
        $this->client->setUseSsl($boolean);
    }
    
    /**
     * @covers MikeBrant\RestClientLib\RestClient::setUseSsl
     * @covers MikeBrant\RestClientLib\RestClient::isUsingSsl
     */
    public function testSetUseSsl() {
        $this->client->setUseSsl(true);
        $this->assertTrue($this->client->isUsingSsl());
        $this->client->setUseSsl(false);
        $this->assertFalse($this->client->isUsingSsl());
    }
    
    /**
     * @dataProvider notBooleanProvider
     * @expectedException \InvalidArgumentException
     * @covers MikeBrant\RestClientLib\RestClient::setUseSslTestMode
     */
    public function testSetUseSslTestModeThrowsExceptions($boolean) {
        $this->client->setUseSslTestMode($boolean);
    }
    
    /**
     * @covers MikeBrant\RestClientLib\RestClient::setUseSslTestMode
     * @covers MikeBrant\RestClientLib\RestClient::isUsingSslTestMode
     */
    public function testSetUseSslTestMode() {
        $this->client->setUseSslTestMode(true);
        $this->assertTrue($this->client->isUsingSslTestMode());
        $this->client->setUseSslTestMode(false);
        $this->assertFalse($this->client->isUsingSslTestMode());
    }
    
    /**
     * @dataProvider emptyProvider
     * @expectedException \InvalidArgumentException
     * @covers MikeBrant\RestClientLib\RestClient::setBasicAuthCredentials
     */
    public function testSetBasicAuthCredentialsThrowsExceptionOnEmptyUser($user) {
        $this->client->setBasicAuthCredentials($user, 'password');
    }
    
    /**
     * @dataProvider emptyProvider
     * @expectedException \InvalidArgumentException
     * @covers MikeBrant\RestClientLib\RestClient::setBasicAuthCredentials
     */
    public function testSetBasicAuthCredentialsThrowsExceptionOnEmptyPassword($password) {
        $this->client->setBasicAuthCredentials('user', $password);
    }
    
    /**
     * @covers MikeBrant\RestClientLib\RestClient::setBasicAuthCredentials
     */
    public function testSetBasicAuthCredentials() {
        $this->client->setBasicAuthCredentials('user', 'password');
        $this->assertAttributeEquals('user', 'basicAuthUsername', $this->client);
        $this->assertAttributeEquals('password', 'basicAuthPassword', $this->client);
        $this->assertAttributeEquals(true, 'useBasicAuth', $this->client);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     * @covers MikeBrant\RestClientLib\RestClient::setHeaders
     */
    public function testSetHeadersThrowsExceptionOnEmptyArray() {
        $this->client->setHeaders(array());
    }
    
    /**
     * @dataProvider headersProvider
     * @covers MikeBrant\RestClientLib\RestClient::setHeaders
     */
    public function testSetHeaders($headers) {
        $this->client->setHeaders($headers);
        $this->assertAttributeEquals($headers, 'headers', $this->client);
    }
    
    /**
     * @dataProvider notZeroOrPositiveIntegerProvider
     * @expectedException \InvalidArgumentException
     * @covers MikeBrant\RestClientLib\RestClient::setTimeout
     */
    public function testSetTimeoutThrowsExceptions($int) {
        $this->client->setTimeout($int);
    }
    
    /**
     * @covers MikeBrant\RestClientLib\RestClient::setTimeout
     * @covers MikeBrant\RestClientLib\RestClient::getTimeout
     */
    public function testSetTimeout() {
        $this->client->setTimeout(30);
        $this->assertEquals(30, $this->client->getTimeout());
        $this->client->setTimeout(0);
        $this->assertEquals(0, $this->client->getTimeout());
    }
    
    /**
     * @dataProvider notBooleanProvider
     * @expectedException \InvalidArgumentException
     * @covers MikeBrant\RestClientLib\RestClient::setFollowRedirects
     */
    public function testSetFollowRedirectsThrowsExceptions($boolean) {
        $this->client->setFollowRedirects($boolean);
    }
    
    /**
     * @covers MikeBrant\RestClientLib\RestClient::setFollowRedirects
     * @covers MikeBrant\RestClientLib\RestClient::isFollowingRedirects
     */
    public function testSetFollowRedirects() {
        $this->client->setFollowRedirects(true);
        $this->assertTrue($this->client->isFollowingRedirects());
        $this->client->setFollowRedirects(false);
        $this->assertFalse($this->client->isFollowingRedirects());
    }
    
    /**
     * @dataProvider notZeroOrPositiveIntegerProvider
     * @expectedException \InvalidArgumentException
     * @covers MikeBrant\RestClientLib\RestClient::setMaxRedirects
     */
    public function testSetMaxRedirectsThrowsExceptions($int) {
        $this->client->setMaxRedirects($int);
    }
    
    /**
     * @covers MikeBrant\RestClientLib\RestClient::setMaxRedirects
     * @covers MikeBrant\RestClientLib\RestClient::getMaxRedirects
     */
    public function testSetMaxRedirects() {
        $this->client->setMaxRedirects(1);
        $this->assertEquals(1, $this->client->getMaxRedirects());
        $this->assertTrue($this->client->isFollowingRedirects());
        $this->client->setMaxRedirects(0);
        $this->assertEquals(0, $this->client->getMaxRedirects());
        $this->assertTrue($this->client->isFollowingRedirects());
    }
    
    /**
     * @expectedException \Exception
     * @covers MikeBrant\RestClientLib\RestClient::curlInit
     */
    public function testCurlInitThrowsException() {
        self::$curlInitResponse = false;
        $this->client->get('action');
    }
    
    /**
     * @expectedException \Exception
     * @covers MikeBrant\RestClientLib\RestClient::curlExec
     */
    public function testCurlExecThrowsException() {
        self::$curlExecResponse = false;
        self::$curlErrorResponse = 'test error';
        $this->client->get('action');
    }
    
    /**
     * @dataProvider buildUriProvider
     * @covers MikeBrant\RestClientLib\RestClient::get
     * @covers MikeBrant\RestClientLib\RestClient::curlSetup
     * @covers MikeBrant\RestClientLib\RestClient::resetRequestResponseProperties
     * @covers MikeBrant\RestClientLib\RestClient::curlInit
     * @covers MikeBrant\RestClientLib\RestClient::setRequestUrl
     * @covers MikeBrant\RestClientLib\RestClient::curlExec
     * @covers MikeBrant\RestClientLib\RestClient::getRequestUrl
     * @covers MikeBrant\RestClientLib\RestClient::getResponseInfo
     * @covers MikeBrant\RestClientLib\RestClient::getRequestHeader
     * @covers MikeBrant\RestClientLib\RestClient::getResponseCode
     * @covers MikeBrant\RestClientLib\RestClient::getResponseBody
     * @covers MikeBrant\RestClientLib\RestClient::curlTeardown
     * @covers MikeBrant\RestClientLib\RestClient::curlClose
     */
    public function testGet($useSsl, $host, $uriBase, $action, $expectedUrl) {
        self::$curlExecResponse = $this->curlExecMockResponse;
        self::$curlGetInfoResponse = $this->curlGetInfoMockResponse;
        $this->client->setBasicAuthCredentials('user', 'password')
                     ->setHeaders(array('header' => 'header value'))
                     ->setUseSsl($useSsl)
                     ->setUseSslTestMode(true)
                     ->setFollowRedirects(true)
                     ->setMaxRedirects(1)
                     ->setremoteHost($host)
                     ->setUriBase($uriBase)
                     ->get($action);
        $this->assertEquals($expectedUrl, $this->client->getRequestUrl());
        $this->assertEquals($this->curlGetInfoMockResponse, $this->client->getResponseInfo());
        $this->assertEquals($this->curlGetInfoMockResponse['request_header'], $this->client->getRequestHeader());
        $this->assertEquals($this->curlGetInfoMockResponse['http_code'], $this->client->getResponseCode());
        $this->assertEquals($this->curlExecMockResponse, $this->client->getResponseBody());
        $this->assertAttributeEquals(null, 'curl', $this->client);
        $this->assertEquals(null, $this->client->getRequestData());
    }
    
    /**
     * @covers MikeBrant\RestClientLib\RestClient::post
     * @covers MikeBrant\RestClientLib\RestClient::setRequestData
     */
    public function testPost() {
        self::$curlExecResponse = $this->curlExecMockResponse;
        self::$curlGetInfoResponse = $this->curlGetInfoMockResponse;
        $this->client->post('', 'test post data');
        $this->assertEquals($this->curlGetInfoMockResponse, $this->client->getResponseInfo());
        $this->assertEquals($this->curlGetInfoMockResponse['request_header'], $this->client->getRequestHeader());
        $this->assertEquals($this->curlGetInfoMockResponse['http_code'], $this->client->getResponseCode());
        $this->assertEquals($this->curlExecMockResponse, $this->client->getResponseBody());
        $this->assertEquals('test post data', $this->client->getRequestData());
        $this->assertAttributeEquals(null, 'curl', $this->client);
   }
    
    /**
     * @covers MikeBrant\RestClientLib\RestClient::put
     */
    public function testPut() {
        self::$curlExecResponse = $this->curlExecMockResponse;
        self::$curlGetInfoResponse = $this->curlGetInfoMockResponse;
        $this->client->put('', 'test put data');
        $this->assertEquals($this->curlGetInfoMockResponse, $this->client->getResponseInfo());
        $this->assertEquals($this->curlGetInfoMockResponse['request_header'], $this->client->getRequestHeader());
        $this->assertEquals($this->curlGetInfoMockResponse['http_code'], $this->client->getResponseCode());
        $this->assertEquals($this->curlExecMockResponse, $this->client->getResponseBody());
        $this->assertEquals('test put data', $this->client->getRequestData());
        $this->assertAttributeEquals(null, 'curl', $this->client);
    }
    
    /**
     * @covers MikeBrant\RestClientLib\RestClient::delete
     */
    public function testDelete() {
        self::$curlExecResponse = $this->curlExecMockResponse;
        self::$curlGetInfoResponse = $this->curlGetInfoMockResponse;
        $this->client->delete('');
        $this->assertEquals($this->curlGetInfoMockResponse, $this->client->getResponseInfo());
        $this->assertEquals($this->curlGetInfoMockResponse['request_header'], $this->client->getRequestHeader());
        $this->assertEquals($this->curlGetInfoMockResponse['http_code'], $this->client->getResponseCode());
        $this->assertEquals($this->curlExecMockResponse, $this->client->getResponseBody());
        $this->assertAttributeEquals(null, 'curl', $this->client);
    }
    
        /**
     * @covers MikeBrant\RestClientLib\RestClient::head
     */
    public function testHead() {
        self::$curlExecResponse = '';
        self::$curlGetInfoResponse = $this->curlGetInfoMockResponse;
        $this->client->head('');
        $this->assertEquals($this->curlGetInfoMockResponse, $this->client->getResponseInfo());
        $this->assertEquals($this->curlGetInfoMockResponse['request_header'], $this->client->getRequestHeader());
        $this->assertEquals($this->curlGetInfoMockResponse['http_code'], $this->client->getResponseCode());
        $this->assertEquals('', $this->client->getResponseBody());
        $this->assertAttributeEquals(null, 'curl', $this->client);
    }
}
