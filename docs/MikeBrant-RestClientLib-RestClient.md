MikeBrant\RestClientLib\RestClient
===============

Class RestClient

Class for executing RESTful service calls using a fluent interface.


* Class name: RestClient
* Namespace: MikeBrant\RestClientLib





Properties
----------


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




### get

    \MikeBrant\RestClientLib\CurlHttpResponse MikeBrant\RestClientLib\RestClient::get(string $action)

Method to execute GET on server



* Visibility: **public**


#### Arguments
* $action **string**



### post

    \MikeBrant\RestClientLib\CurlHttpResponse MikeBrant\RestClientLib\RestClient::post(mixed $action, mixed $data)

Method to exexute POST on server



* Visibility: **public**


#### Arguments
* $action **mixed**
* $data **mixed**



### put

    \MikeBrant\RestClientLib\CurlHttpResponse MikeBrant\RestClientLib\RestClient::put(string $action, mixed $data)

Method to execute PUT on server



* Visibility: **public**


#### Arguments
* $action **string**
* $data **mixed**



### delete

    \MikeBrant\RestClientLib\CurlHttpResponse MikeBrant\RestClientLib\RestClient::delete(string $action)

Method to execute DELETE on server



* Visibility: **public**


#### Arguments
* $action **string**



### head

    \MikeBrant\RestClientLib\CurlHttpResponse MikeBrant\RestClientLib\RestClient::head(string $action)

Method to execute HEAD on server



* Visibility: **public**


#### Arguments
* $action **string**



### setRemoteHost

    \MikeBrant\RestClientLib\RestClient MikeBrant\RestClientLib\RestClient::setRemoteHost(string $host)

Sets host name of remote server



* Visibility: **public**


#### Arguments
* $host **string**



### setUriBase

    \MikeBrant\RestClientLib\RestClient MikeBrant\RestClientLib\RestClient::setUriBase(string $uriBase)

Sets URI base for the instance



* Visibility: **public**


#### Arguments
* $uriBase **string**



### setUseSsl

    \MikeBrant\RestClientLib\RestClient MikeBrant\RestClientLib\RestClient::setUseSsl(boolean $value)

Sets whether SSL is to be used



* Visibility: **public**


#### Arguments
* $value **boolean**



### setUseSslTestMode

    \MikeBrant\RestClientLib\RestClient MikeBrant\RestClientLib\RestClient::setUseSslTestMode(boolean $value)

Sets whether SSL Test Mode is to be used



* Visibility: **public**


#### Arguments
* $value **boolean**



### setBasicAuthCredentials

    \MikeBrant\RestClientLib\RestClient MikeBrant\RestClientLib\RestClient::setBasicAuthCredentials(string $user, string $password)

Sets basic authentication credentials



* Visibility: **public**


#### Arguments
* $user **string**
* $password **string**



### setHeaders

    \MikeBrant\RestClientLib\RestClient MikeBrant\RestClientLib\RestClient::setHeaders(array $headers)

Sets HTTP headers from an associative array where key is header name and value is the header value



* Visibility: **public**


#### Arguments
* $headers **array**



### setTimeout

    \MikeBrant\RestClientLib\RestClient MikeBrant\RestClientLib\RestClient::setTimeout(integer $seconds)

Sets maximum timeout for curl requests



* Visibility: **public**


#### Arguments
* $seconds **integer**



### setFollowRedirects

    \MikeBrant\RestClientLib\RestClient MikeBrant\RestClientLib\RestClient::setFollowRedirects(boolean $follow)

Sets flag on whether to follow 3XX redirects.



* Visibility: **public**


#### Arguments
* $follow **boolean**



### setMaxRedirects

    \MikeBrant\RestClientLib\RestClient MikeBrant\RestClientLib\RestClient::setMaxRedirects(integer $redirects)

Sets maximum number of redirects to follow. A value of 0 represents no redirect limit. Also sets followRedirects property to true .



* Visibility: **public**


#### Arguments
* $redirects **integer**



### getRemoteHost

    string MikeBrant\RestClientLib\RestClient::getRemoteHost()

Get remote host setting



* Visibility: **public**




### getUriBase

    string MikeBrant\RestClientLib\RestClient::getUriBase()

Get URI Base setting



* Visibility: **public**




### isUsingSsl

    boolean MikeBrant\RestClientLib\RestClient::isUsingSsl()

Get boolean setting indicating whether SSL is to be used



* Visibility: **public**




### isUsingSslTestMode

    boolean MikeBrant\RestClientLib\RestClient::isUsingSslTestMode()

Get boolean setting indicating whether SSL test mode is enabled



* Visibility: **public**




### getTimeout

    integer MikeBrant\RestClientLib\RestClient::getTimeout()

Get timeout setting



* Visibility: **public**




### isFollowingRedirects

    boolean MikeBrant\RestClientLib\RestClient::isFollowingRedirects()

Get follow redirects setting



* Visibility: **public**




### getMaxRedirects

    integer MikeBrant\RestClientLib\RestClient::getMaxRedirects()

Get max redirects setting



* Visibility: **public**




### curlSetup

    void MikeBrant\RestClientLib\RestClient::curlSetup()

Method to set up curl handle on client



* Visibility: **private**




### curlInit

    resource MikeBrant\RestClientLib\RestClient::curlInit()

Method to initilize and return a curl handle



* Visibility: **protected**




### curlTeardown

    void MikeBrant\RestClientLib\RestClient::curlTeardown()

Method to to teardown curl fixtures at end of request



* Visibility: **private**




### curlClose

    void MikeBrant\RestClientLib\RestClient::curlClose(resource $curl)

Method to close curl handle



* Visibility: **protected**


#### Arguments
* $curl **resource** - &lt;p&gt;curl handle&lt;/p&gt;



### curlExec

    \MikeBrant\RestClientLib\CurlHttpResponse MikeBrant\RestClientLib\RestClient::curlExec()

Method to execute curl call



* Visibility: **private**




### setRequestUrl

    void MikeBrant\RestClientLib\RestClient::setRequestUrl(string $action)

Method to set the url on curl handle based on passed action



* Visibility: **protected**


#### Arguments
* $action **string**



### buildUrl

    string MikeBrant\RestClientLib\RestClient::buildUrl(string $action)

Method to build URL based on class settings and passed action



* Visibility: **protected**


#### Arguments
* $action **string**



### setRequestData

    void MikeBrant\RestClientLib\RestClient::setRequestData(mixed $data)

Method to set data to be sent along with POST/PUT requests



* Visibility: **protected**


#### Arguments
* $data **mixed**



### validateAction

    void MikeBrant\RestClientLib\RestClient::validateAction(string $action)

Method to provide common validation for action parameters



* Visibility: **protected**


#### Arguments
* $action **string**



### validateData

    void MikeBrant\RestClientLib\RestClient::validateData(mixed $data)

Method to provide common validation for data parameters



* Visibility: **protected**


#### Arguments
* $data **mixed**


