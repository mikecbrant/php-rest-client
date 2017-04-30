MikeBrant\RestClientLib\RestMultiClient
===============

Class RestMultiClient

Class which extendd RestClient to provide curl_multi capabilities, allowing for multiple REST calls to be made in parallel.


* Class name: RestMultiClient
* Namespace: MikeBrant\RestClientLib
* Parent class: [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)





Properties
----------


### $curlHandles

    private array<mixed,resource> $curlHandles = array()

Store array of curl handles for multi_exec



* Visibility: **private**


### $curlMultiHandle

    private resource $curlMultiHandle = null

Stores curl multi handle to which individual handles in curlHandles are added.



* Visibility: **private**


### $maxHandles

    private integer $maxHandles = 10

Variable to store the maximum number of handles to be used for curl_multi_exec



* Visibility: **private**


### $useBasicAuth

    protected boolean $useBasicAuth = false

Flag to determine if basic authentication is to be used.



* Visibility: **protected**


### $basicAuthUsername

    protected string $basicAuthUsername = null

User Name for HTTP Basic Auth



* Visibility: **protected**


### $basicAuthPassword

    protected string $basicAuthPassword = null

Password for HTTP Basic Auth



* Visibility: **protected**


### $useSsl

    protected boolean $useSsl = false

Flag to determine if SSL is used



* Visibility: **protected**


### $useSslTestMode

    protected boolean $useSslTestMode = false

Flag to determine is we are to run in test mode where host's SSL cert is not verified



* Visibility: **protected**


### $timeout

    protected integer $timeout = 30

Integer value representing number of seconds to set for curl timeout option. Defaults to 30 seconds.



* Visibility: **protected**


### $remoteHost

    protected string $remoteHost = null

Variable to store remote host name



* Visibility: **protected**


### $followRedirects

    protected boolean $followRedirects = false

Variable to hold setting to determine if redirects are followed



* Visibility: **protected**


### $maxRedirects

    protected integer $maxRedirects

Variable to hold value for maximum number of redirects to follow for cases when redirect are being followed.

Default value of 0 will allow for following of unlimited redirects.

* Visibility: **protected**


### $uriBase

    protected string $uriBase = '/'

Variable which can hold a URI base for all actions



* Visibility: **protected**


### $curl

    private mixed $curl = null

Stores curl handle



* Visibility: **private**


### $headers

    private array $headers = array()

Array containing headers to be used for request



* Visibility: **private**


Methods
-------


### __construct

    void MikeBrant\RestClientLib\RestClient::__construct()

Constructor method. Currently there is no instantiation logic.



* Visibility: **public**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)




### get

    \MikeBrant\RestClientLib\CurlHttpResponse MikeBrant\RestClientLib\RestClient::get(string $action)

Method to execute GET on server



* Visibility: **public**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)


#### Arguments
* $action **string**



### post

    \MikeBrant\RestClientLib\CurlHttpResponse MikeBrant\RestClientLib\RestClient::post(mixed $action, mixed $data)

Method to exexute POST on server



* Visibility: **public**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)


#### Arguments
* $action **mixed**
* $data **mixed**



### put

    \MikeBrant\RestClientLib\CurlHttpResponse MikeBrant\RestClientLib\RestClient::put(string $action, mixed $data)

Method to execute PUT on server



* Visibility: **public**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)


#### Arguments
* $action **string**
* $data **mixed**



### delete

    \MikeBrant\RestClientLib\CurlHttpResponse MikeBrant\RestClientLib\RestClient::delete(string $action)

Method to execute DELETE on server



* Visibility: **public**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)


#### Arguments
* $action **string**



### head

    \MikeBrant\RestClientLib\CurlHttpResponse MikeBrant\RestClientLib\RestClient::head(string $action)

Method to execute HEAD on server



* Visibility: **public**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)


#### Arguments
* $action **string**



### setMaxHandles

    \MikeBrant\RestClientLib\RestMultiClient MikeBrant\RestClientLib\RestMultiClient::setMaxHandles(integer $maxHandles)

Sets maximum number of handles that will be instantiated for curl_multi_exec calls



* Visibility: **public**


#### Arguments
* $maxHandles **integer**



### getMaxHandles

    integer MikeBrant\RestClientLib\RestMultiClient::getMaxHandles()

Getter for maxHandles setting



* Visibility: **public**




### curlMultiSetup

    void MikeBrant\RestClientLib\RestMultiClient::curlMultiSetup(integer $handlesNeeded)

Method to set up a given number of curl handles for use with curl_multi_exec



* Visibility: **private**


#### Arguments
* $handlesNeeded **integer**



### curlMultiTeardown

    void MikeBrant\RestClientLib\RestMultiClient::curlMultiTeardown()

Method to reset the curlMultiHandle and all individual curlHandles related to it.



* Visibility: **private**




### curlMultiExec

    \MikeBrant\RestClientLib\CurlMultiHttpResponse MikeBrant\RestClientLib\RestMultiClient::curlMultiExec()

Method to execute curl_multi call



* Visibility: **private**




### setRequestUrls

    void MikeBrant\RestClientLib\RestMultiClient::setRequestUrls(array<mixed,string> $actions)

Method to set the urls for  multi_exec action



* Visibility: **private**


#### Arguments
* $actions **array&lt;mixed,string&gt;**



### setRequestDataArray

    void MikeBrant\RestClientLib\RestMultiClient::setRequestDataArray(array<mixed,mixed> $data)

Method to set array of data to be sent along with multi_exec POST/PUT requests



* Visibility: **private**


#### Arguments
* $data **array&lt;mixed,mixed&gt;**



### validateInputArrays

    void MikeBrant\RestClientLib\RestMultiClient::validateInputArrays(array<mixed,string> $actions, array<mixed,mixed> $data)

Method to provide validation to action and data arrays for POST/PUT methods



* Visibility: **private**


#### Arguments
* $actions **array&lt;mixed,string&gt;**
* $data **array&lt;mixed,mixed&gt;**



### validateActionArray

    void MikeBrant\RestClientLib\RestMultiClient::validateActionArray(array<mixed,string> $actions)

Method to provide common validation for action array parameters



* Visibility: **private**


#### Arguments
* $actions **array&lt;mixed,string&gt;**



### validateDataArray

    void MikeBrant\RestClientLib\RestMultiClient::validateDataArray(array<mixed,mixed> $data)

Method to provide common validation for data array parameters



* Visibility: **private**


#### Arguments
* $data **array&lt;mixed,mixed&gt;**



### setRemoteHost

    \MikeBrant\RestClientLib\RestClient MikeBrant\RestClientLib\RestClient::setRemoteHost(string $host)

Sets host name of remote server



* Visibility: **public**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)


#### Arguments
* $host **string**



### setUriBase

    \MikeBrant\RestClientLib\RestClient MikeBrant\RestClientLib\RestClient::setUriBase(string $uriBase)

Sets URI base for the instance



* Visibility: **public**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)


#### Arguments
* $uriBase **string**



### setUseSsl

    \MikeBrant\RestClientLib\RestClient MikeBrant\RestClientLib\RestClient::setUseSsl(boolean $value)

Sets whether SSL is to be used



* Visibility: **public**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)


#### Arguments
* $value **boolean**



### setUseSslTestMode

    \MikeBrant\RestClientLib\RestClient MikeBrant\RestClientLib\RestClient::setUseSslTestMode(boolean $value)

Sets whether SSL Test Mode is to be used



* Visibility: **public**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)


#### Arguments
* $value **boolean**



### setBasicAuthCredentials

    \MikeBrant\RestClientLib\RestClient MikeBrant\RestClientLib\RestClient::setBasicAuthCredentials(string $user, string $password)

Sets basic authentication credentials



* Visibility: **public**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)


#### Arguments
* $user **string**
* $password **string**



### setHeaders

    \MikeBrant\RestClientLib\RestClient MikeBrant\RestClientLib\RestClient::setHeaders(array $headers)

Sets HTTP headers from an associative array where key is header name and value is the header value



* Visibility: **public**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)


#### Arguments
* $headers **array**



### setTimeout

    \MikeBrant\RestClientLib\RestClient MikeBrant\RestClientLib\RestClient::setTimeout(integer $seconds)

Sets maximum timeout for curl requests



* Visibility: **public**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)


#### Arguments
* $seconds **integer**



### setFollowRedirects

    \MikeBrant\RestClientLib\RestClient MikeBrant\RestClientLib\RestClient::setFollowRedirects(boolean $follow)

Sets flag on whether to follow 3XX redirects.



* Visibility: **public**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)


#### Arguments
* $follow **boolean**



### setMaxRedirects

    \MikeBrant\RestClientLib\RestClient MikeBrant\RestClientLib\RestClient::setMaxRedirects(integer $redirects)

Sets maximum number of redirects to follow. A value of 0 represents no redirect limit. Also sets followRedirects property to true .



* Visibility: **public**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)


#### Arguments
* $redirects **integer**



### getRemoteHost

    string MikeBrant\RestClientLib\RestClient::getRemoteHost()

Get remote host setting



* Visibility: **public**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)




### getUriBase

    string MikeBrant\RestClientLib\RestClient::getUriBase()

Get URI Base setting



* Visibility: **public**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)




### isUsingSsl

    boolean MikeBrant\RestClientLib\RestClient::isUsingSsl()

Get boolean setting indicating whether SSL is to be used



* Visibility: **public**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)




### isUsingSslTestMode

    boolean MikeBrant\RestClientLib\RestClient::isUsingSslTestMode()

Get boolean setting indicating whether SSL test mode is enabled



* Visibility: **public**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)




### getTimeout

    integer MikeBrant\RestClientLib\RestClient::getTimeout()

Get timeout setting



* Visibility: **public**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)




### isFollowingRedirects

    boolean MikeBrant\RestClientLib\RestClient::isFollowingRedirects()

Get follow redirects setting



* Visibility: **public**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)




### getMaxRedirects

    integer MikeBrant\RestClientLib\RestClient::getMaxRedirects()

Get max redirects setting



* Visibility: **public**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)




### curlSetup

    void MikeBrant\RestClientLib\RestClient::curlSetup()

Method to set up curl handle on client



* Visibility: **private**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)




### curlInit

    resource MikeBrant\RestClientLib\RestClient::curlInit()

Method to initilize and return a curl handle



* Visibility: **protected**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)




### curlTeardown

    void MikeBrant\RestClientLib\RestClient::curlTeardown()

Method to to teardown curl fixtures at end of request



* Visibility: **private**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)




### curlClose

    void MikeBrant\RestClientLib\RestClient::curlClose(resource $curl)

Method to close curl handle



* Visibility: **protected**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)


#### Arguments
* $curl **resource** - &lt;p&gt;curl handle&lt;/p&gt;



### curlExec

    \MikeBrant\RestClientLib\CurlHttpResponse MikeBrant\RestClientLib\RestClient::curlExec()

Method to execute curl call



* Visibility: **private**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)




### setRequestUrl

    void MikeBrant\RestClientLib\RestClient::setRequestUrl(string $action)

Method to set the url on curl handle based on passed action



* Visibility: **protected**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)


#### Arguments
* $action **string**



### buildUrl

    string MikeBrant\RestClientLib\RestClient::buildUrl(string $action)

Method to build URL based on class settings and passed action



* Visibility: **protected**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)


#### Arguments
* $action **string**



### setRequestData

    void MikeBrant\RestClientLib\RestClient::setRequestData(mixed $data)

Method to set data to be sent along with POST/PUT requests



* Visibility: **protected**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)


#### Arguments
* $data **mixed**



### validateAction

    void MikeBrant\RestClientLib\RestClient::validateAction(string $action)

Method to provide common validation for action parameters



* Visibility: **protected**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)


#### Arguments
* $action **string**



### validateData

    void MikeBrant\RestClientLib\RestClient::validateData(mixed $data)

Method to provide common validation for data parameters



* Visibility: **protected**
* This method is defined by [MikeBrant\RestClientLib\RestClient](MikeBrant-RestClientLib-RestClient.md)


#### Arguments
* $data **mixed**


