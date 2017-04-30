MikeBrant\RestClientLib\CurlMultiHttpResponse
===============

Class CurlMultiHttpResponse

Class representing a collection of HTTP responses as returned from
multiple parallel curl calls.


* Class name: CurlMultiHttpResponse
* Namespace: MikeBrant\RestClientLib





Properties
----------


### $curlHttpResponses

    protected array<mixed,\MikeBrant\RestClientLib\CurlHttpResponse> $curlHttpResponses = array()

Variable to store individual CurlHttpResponse objects from curl_multi call



* Visibility: **protected**


Methods
-------


### __construct

    mixed MikeBrant\RestClientLib\CurlMultiHttpResponse::__construct()

Constructor method. Currently there is no instantiation logic.



* Visibility: **public**




### addResponse

    void MikeBrant\RestClientLib\CurlMultiHttpResponse::addResponse(\MikeBrant\RestClientLib\CurlHttpResponse $response)

Method to add CurlHttpResponse object to collection



* Visibility: **public**


#### Arguments
* $response **[MikeBrant\RestClientLib\CurlHttpResponse](MikeBrant-RestClientLib-CurlHttpResponse.md)**



### getCurlHttpResponses

    array<mixed,\MikeBrant\RestClientLib\CurlHttpResponse> MikeBrant\RestClientLib\CurlMultiHttpResponse::getCurlHttpResponses()

Returns array of all CurlHttpResponse objects in collection.



* Visibility: **public**




### getAll

    array<mixed,\MikeBrant\RestClientLib\CurlHttpResponse> MikeBrant\RestClientLib\CurlMultiHttpResponse::getAll()

Alias for getCurlHttpResponses



* Visibility: **public**




### getResponseBodies

    array<mixed,mixed> MikeBrant\RestClientLib\CurlMultiHttpResponse::getResponseBodies()

Returns array of response bodies for each response in collection.



* Visibility: **public**




### getHttpCodes

    array<mixed,integer> MikeBrant\RestClientLib\CurlMultiHttpResponse::getHttpCodes()

Returns array of response codes for each response in collection.



* Visibility: **public**




### getRequestUrls

    array<mixed,string> MikeBrant\RestClientLib\CurlMultiHttpResponse::getRequestUrls()

Returns array of URL's used for each response in collectoin as returned via curl_getinfo.



* Visibility: **public**




### getRequestHeaders

    array<mixed,string> MikeBrant\RestClientLib\CurlMultiHttpResponse::getRequestHeaders()

Returns array of request headers for each response in collection as returned via curl_getinfo.



* Visibility: **public**




### getCurlGetinfoArrays

    array<mixed,array> MikeBrant\RestClientLib\CurlMultiHttpResponse::getCurlGetinfoArrays()

Returns array of curl_getinfo arrays for each response in collection.

See documentation at http://php.net/manual/en/function.curl-getinfo.php for expected format for each array element.

* Visibility: **public**



