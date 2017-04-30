<?php

namespace MikeBrant\RestClientLib;

use PHPUnit\Framework\TestCase;

/**
 * Class CurlHttpResponseTest
 *
 * @package MikeBrant\RestClientLib
 */
class CurlHttpResponseTest extends TestCase
{
    /**
     * @var string
     */
    protected $curlExecMockResponse = 'Test Response';

    /**
     * @var array
     */
    protected $curlGetinfoMockResponse = [
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
        'certinfo' => [],
        'primary_port' => 80,
        'local_ip' => '192.168.1.74',
        'local_port' => 59733,
        'request_header' => "GET / HTTP/1.1\nHost: google.com\nAccept: */*",
    ];

    /**
     * @return array
     */
    public function invalidGetinfoProvider()
    {
        return [
            [[]],
            [['no keys']],
            [['http_code' => 'not integer']],
            [['http_code' => 200]],
            [[
                'http_code' => 200,
                'content_type' => false
            ]],
            [[
                'http_code' => 200,
                'content_type' => 'text/html'
            ]],
            [[
                'http_code' => 200,
                'content_type' => 'text/html',
                'url' => false
            ]],
            [[
                'http_code' => 200,
                'content_type' => 'text/html',
                'url' => 'htttp://somedomain.com'
            ]],
            [[
                'http_code' => 200,
                'content_type' => 'text/html',
                'url' => 'htttp://somedomain.com',
                'request_header' => false
            ]]
        ];
    }
    
    /**
     * @dataProvider invalidGetinfoProvider
     * @expectedException \InvalidArgumentException
     * @covers \MikeBrant\RestClientLib\CurlHttpResponse::validateGetinfoArray
     */
    public function testValidateGetinfoArrayThrowsExceptions($getinfo)
    {
        new CurlHttpResponse('test', $getinfo);
    }
    
    /**
     * @covers \MikeBrant\RestClientLib\CurlHttpResponse::__construct
     * @covers \MikeBrant\RestClientLib\CurlHttpResponse::validateGetinfoArray
     * @covers \MikeBrant\RestClientLib\CurlHttpResponse::getBody
     * @covers \MikeBrant\RestClientLib\CurlHttpResponse::getHttpCode
     * @covers \MikeBrant\RestClientLib\CurlHttpResponse::getRequestUrl
     * @covers \MikeBrant\RestClientLib\CurlHttpResponse::getRequestHeader
     * @covers \MikeBrant\RestClientLib\CurlHttpResponse::getCurlGetinfo
     */
    public function testConstructor()
    {
        $response = new CurlHttpResponse($this->curlExecMockResponse, $this->curlGetinfoMockResponse);
        $this->assertEquals($this->curlExecMockResponse, $response->getBody());
        $this->assertEquals(
            $this->curlGetinfoMockResponse['http_code'],
            $response->getHttpCode()
        );
        $this->assertEquals(
            $this->curlGetinfoMockResponse['url'],
            $response->getRequestUrl()
        );
        $this->assertEquals(
            $this->curlGetinfoMockResponse['request_header'],
            $response->getRequestHeader()
        );
        $this->assertEquals(
            $this->curlGetinfoMockResponse,
            $response->getCurlGetinfo()
        );
    }
}
