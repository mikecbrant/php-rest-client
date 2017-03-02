# php-rest-client

This library provides classes to implement basic REST clients based on PHP's cURL extension.  Two client classes are made available:

- **RestClient** - a class for executing RESTful service calls using a fluent interface.
- **RestMultiClient** - a class which extends RestClient to provide curl_multi capabilities to allow for multiple REST calls to be made in parallel.

These classes support:
- HTTP actions - GET, POST, PUT, DELETE, and HEAD
- Basic authentication
- SSL, with the ability to toggle SSL certificate validation to help in development/test enviroments

Requires:
- PHP 5.6+ (Likely to work in PHP 5.1+, but not tested below 5.6)
- PHP cURL extension
- PHPUnit 5.7+ (for unit tests only)

This version represents a total re-factoring from previous versions of this library, which were getting long in the tooth and were out of compliance with more modern PHP development standards (i.e. PSR) and tools (i.e. composer).

**Usage example:**

```
$restClient = new RestClient();
$restClient->setRemoteHost('foo.bar.com')                 [Host name must be set]
    ->setUriBase('/some_service/')                        [Optional, default is '/']
    ->setUseSsl(true)                                     [Optional, default is false]
    ->setUseSslTestMode(false)                            [Optional, default is false]
    ->setBasicAuthCredentials('username', 'password')     [Optional]
    ->setHeaders(array('Accept' => 'application/json'))   [Optional, if not specified, default cURL headers for each request type will be used]
    ->get('resource')                                     [Perform HTTP GET on URL [hostname].[uriBase].[resource parameter passed to method]]
    ->post('resource', [data])                            [Perform HTTP POST on passed resource reference, data can be in form allowed by curl_setopt CURLOPT_POSTFIELDS]
    ->put('resource', [data])                             [Perform HTTP PUT on passed resource reference, data can be in form allowed by curl_setopt CURLOPT_POSTFIELDS]
    ->delete('resource');                                 [Perform HTTP DELETE on passed resource reference]
    ->head('resource');                                   [Perform HTTP HEAD on passed resource reference]
```