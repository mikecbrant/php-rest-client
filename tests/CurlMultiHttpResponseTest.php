<?php

namespace MikeBrant\RestClientLib;

use PHPUnit\Framework\TestCase;

/**
 * Class CurlMultiHttpResponseTest
 *
 * @package MikeBrant\RestClientLib
 */
class CurlMultiHttpResponseTest extends TestCase
{
    /**
     * @var CurlMultiHttpResponse
     */
    protected $curlMultiHttpResponse = null;

    /**
     * @var string
     */
    protected $curlExecMockResponse = 'Test Response';

    /**
     * @var array
     */
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

    /**
     * Test setUp method
     */
    protected function setUp()
    {
        $this->curlMultiHttpResponse = new CurlMultiHttpResponse();
    }

    /**
     * @return array
     */
    public function curlHttpResponseProvider()
    {
        return [
            [new CurlHttpResponse(
                $this->curlExecMockResponse,
                $this->curlGetinfoMockResponse
            )]
        ];
    }

    /**
     * @dataProvider curlHttpResponseProvider
     * @covers \MikeBrant\RestClientLib\CurlMultiHttpResponse::addResponse
     * @covers \MikeBrant\RestClientLib\CurlMultiHttpResponse::getCurlHttpResponses
     * @covers \MikeBrant\RestClientLib\CurlMultiHttpResponse::getAll
     */
    public function testAddResponse(CurlHttpResponse $curlHttpResponse)
    {
        $responseArray = array_fill(0, 5, $curlHttpResponse);
        for ($i = 0; $i < count($responseArray); $i++) {
            $this->curlMultiHttpResponse->addResponse($curlHttpResponse);
        }
        $this->assertEquals(
            $responseArray,
            $this->curlMultiHttpResponse->getCurlHttpResponses()
        );
        $this->assertEquals($responseArray, $this->curlMultiHttpResponse->getAll());
    }

    /**
     * @dataProvider curlHttpResponseProvider
     * @covers \MikeBrant\RestClientLib\CurlMultiHttpResponse::getResponseBodies
     */
    public function testGetResponseBodies(CurlHttpResponse $curlHttpResponse)
    {
        $responseArray = array_fill(0, 5, $curlHttpResponse);
        for ($i = 0; $i < count($responseArray); $i++) {
            $this->curlMultiHttpResponse->addResponse($curlHttpResponse);
        }
        $responseBodies = array_map(
            function(CurlHttpResponse $val) {
                return $val->getBody();
            },
            $responseArray
        );
        $this->assertEquals(
            $responseBodies,
            $this->curlMultiHttpResponse->getResponseBodies()
        );
    }

    /**
     * @dataProvider curlHttpResponseProvider
     * @covers \MikeBrant\RestClientLib\CurlMultiHttpResponse::getHttpCodes
     */
    public function testgetHttpCodes(CurlHttpResponse $curlHttpResponse)
    {
        $responseArray = array_fill(0, 5, $curlHttpResponse);
        for ($i = 0; $i < count($responseArray); $i++) {
            $this->curlMultiHttpResponse->addResponse($curlHttpResponse);
        }
        $responseCodes = array_map(
            function(CurlHttpResponse $val) {
                return $val->getHttpCode();
            },
            $responseArray
        );
        $this->assertEquals(
            $responseCodes,
            $this->curlMultiHttpResponse->getHttpCodes()
        );
    }

    /**
     * @dataProvider curlHttpResponseProvider
     * @covers \MikeBrant\RestClientLib\CurlMultiHttpResponse::getRequestUrls
     */
    public function testGetRequestUrls(CurlHttpResponse $curlHttpResponse)
    {
        $responseArray = array_fill(0, 5, $curlHttpResponse);
        for ($i = 0; $i < count($responseArray); $i++) {
            $this->curlMultiHttpResponse->addResponse($curlHttpResponse);
        }
        $requestUrls = array_map(
            function(CurlHttpResponse $val) {
                return $val->getRequestUrl();
            },
            $responseArray
        );
        $this->assertEquals(
            $requestUrls,
            $this->curlMultiHttpResponse->getRequestUrls()
        );
    }

    /**
     * @dataProvider curlHttpResponseProvider
     * @covers \MikeBrant\RestClientLib\CurlMultiHttpResponse::getRequestHeaders
     */
    public function testGetRequestHeaders(CurlHttpResponse $curlHttpResponse)
    {
        $responseArray = array_fill(0, 5, $curlHttpResponse);
        for ($i = 0; $i < count($responseArray); $i++) {
            $this->curlMultiHttpResponse->addResponse($curlHttpResponse);
        }
        $requestHeaders = array_map(
            function(CurlHttpResponse $val) {
                return $val->getRequestHeader();
            },
            $responseArray
        );
        $this->assertEquals(
            $requestHeaders,
            $this->curlMultiHttpResponse->getRequestHeaders()
        );
    }

    /**
     * @dataProvider curlHttpResponseProvider
     * @covers \MikeBrant\RestClientLib\CurlMultiHttpResponse::getCurlGetinfoArrays
     */
    public function testGetCurlGetinfoArrays(CurlHttpResponse $curlHttpResponse)
    {
        $responseArray = array_fill(0, 5, $curlHttpResponse);
        for ($i = 0; $i < count($responseArray); $i++) {
            $this->curlMultiHttpResponse->addResponse($curlHttpResponse);
        }
        $requestInfoArrays = array_map(
            function(CurlHttpResponse $val) {
                return $val->getCurlGetinfo();
            },
            $responseArray
        );
        $this->assertEquals(
            $requestInfoArrays,
            $this->curlMultiHttpResponse->getCurlGetinfoArrays()
        );
    }
}
