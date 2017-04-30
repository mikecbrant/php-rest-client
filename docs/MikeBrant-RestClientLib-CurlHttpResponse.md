MikeBrant\RestClientLib\CurlHttpResponse
===============

Class CurlHttpResponse

Class representing HTTP response as returned from curl call.


* Class name: CurlHttpResponse
* Namespace: MikeBrant\RestClientLib





Properties
----------


### $body

    protected mixed $body = null

Variable to store response body



* Visibility: **protected**


### $httpCode

    protected integer $httpCode = null

Variable to store HTTP repsonse code



* Visibility: **protected**


### $contentType

    protected string $contentType = null

Variable to store response content type header



* Visibility: **protected**


### $requestUrl

    protected string $requestUrl = null

Variable to store URL used in request as reported via curl_getinfo().



* Visibility: **protected**


### $requestHeader

    protected string $requestHeader = null

Variable to store header used in request as reported via curl_getinfo().



* Visibility: **protected**


### $curlGetinfo

    protected array $curlGetinfo = null

Variable to store curl getinfo array.

See documentation at http://php.net/manual/en/function.curl-getinfo.php for expected array format.

* Visibility: **protected**


Methods
-------


### __construct

    mixed MikeBrant\RestClientLib\CurlHttpResponse::__construct(mixed $responseBody, array $curlGetinfo)

Constructor method.



* Visibility: **public**


#### Arguments
* $responseBody **mixed** - &lt;p&gt;Response body as returned from a curl request.&lt;/p&gt;
* $curlGetinfo **array** - &lt;p&gt;Array returned form curl_getinfo() function call for request.&lt;/p&gt;



### getBody

    mixed MikeBrant\RestClientLib\CurlHttpResponse::getBody()

Returns response body for request



* Visibility: **public**




### getHttpCode

    integer MikeBrant\RestClientLib\CurlHttpResponse::getHttpCode()

Returns HTTP response code for request



* Visibility: **public**




### getRequestUrl

    string MikeBrant\RestClientLib\CurlHttpResponse::getRequestUrl()

Returns URL used in request as reported via curl_getinfo().



* Visibility: **public**




### getRequestHeader

    string MikeBrant\RestClientLib\CurlHttpResponse::getRequestHeader()

Returns header used in request as reported via curl_getinfo().



* Visibility: **public**




### getCurlGetinfo

    array MikeBrant\RestClientLib\CurlHttpResponse::getCurlGetinfo()

Returns curl getinfo array.

See documentation at http://php.net/manual/en/function.curl-getinfo.php for expected array format.

* Visibility: **public**




### validateGetinfoArray

    mixed MikeBrant\RestClientLib\CurlHttpResponse::validateGetinfoArray(array $getinfo)

Method to perform minimal validation of input array as having keys expected to be returned from
curl_getinfo().



* Visibility: **protected**


#### Arguments
* $getinfo **array** - &lt;p&gt;Array as returned from curl_getinfo()&lt;/p&gt;


