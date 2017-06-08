[![Build Status](https://travis-ci.org/mikecbrant/php-rest-client.svg?branch=master)](https://travis-ci.org/mikecbrant/php-rest-client)
[![Code Climate](https://codeclimate.com/github/mikecbrant/php-rest-client/badges/gpa.svg)](https://codeclimate.com/github/mikecbrant/php-rest-client)
[![Test Coverage](https://codeclimate.com/github/mikecbrant/php-rest-client/badges/coverage.svg)](https://codeclimate.com/github/mikecbrant/php-rest-client/coverage)

# php-rest-client

This library provides classes implementing basic REST clients based on PHP's cURL extension.  Two client classes are made available:

- [RestClient](docs/MikeBrant-RestClientLib-RestClient.md) - a class for executing RESTful service calls.
- [RestMultiClient](docs/MikeBrant-RestClientLib-RestMultiClient.md) - a class which extends RestClient to provide 
curl_multi capabilities to allow multiple RESTful calls to be made in parallel.

Additionally, this library provides classes which wrap curl responses within object oriented interface:
- [CurlHttpResponse](docs/MikeBrant-RestClientLib-CurlHttpResponse.md) - a class which encapsulates an HTTP response 
received via cURL into a class wrapper.
- [CurlMultiHttpResponse](docs/MikeBrant-RestClientLib-CurlMultiHttpResponse.md) - a class which represents a collection of 
CurlHttpRepsonse objects as returned from multiple parallel cURL calls.

These classes support:
- HTTP actions - GET, POST, PUT, DELETE, and HEAD
- Basic authentication
- SSL, with the ability to toggle SSL certificate validation to help in development/test enviroments

Requires:
- PHP 5.6+
- PHP cURL extension
- PHPUnit 5.7+ (for unit tests only)

This library is developed against PHP 7.1 and tested via Travis CI against:
- PHP 5.6.*
- PHP 7.0.*
- PHP 7.1.*
- PHP Nightly build

[Full library documentation](/docs/RestClientLib.md)

[Travis CI build status](https://travis-ci.org/mikecbrant/php-rest-client)

[Code Climate code coverage and health information](https://codeclimate.com/github/mikecbrant/php-rest-client)

[Packagist page](https://packagist.org/packages/mikecbrant/php-rest-client)



**Usage example:**

```
<?php

use MikeBrant\RestClientLib;

/**
 * Single request using RestClient
 */
$restClient = new RestClient();
$restClient->setRemoteHost('foo.bar.com')
           ->setUriBase('/some_service/')
           ->setUseSsl(true)
           ->setUseSslTestMode(false)
           ->setBasicAuthCredentials('username', 'password')
           ->setHeaders(array('Accept' => 'application/json'));
// make requests against service
$response = $restClient->get('resource');
$response = $restClient->post('resource', $data);
$response = $restClient->put('resource', $data);
$response = $restClient->delete('resource');
$response = $restClient->head('resource');

/**
 * Multiple parallel requests using RestMultiClient
 */
$restMultiClient = new RestMultiClient();
$restMultiClient->setRemoteHost('foo.bar.com')
                ->setUriBase('/some_service/')
                ->setUseSsl(true)
                ->setUseSslTestMode(false)
                ->setBasicAuthCredentials('username', 'password')
                ->setHeaders(array('Accept' => 'application/json'));
// make requests against service
$responses = $restMultiClient->get(['resource1', 'resource2', ...]);
$responses = $restMultiClient->post(['resource1', 'resource2', ...], [$data1, $data2, ...]);
$responses = $restMultiClient->put(['resource1', 'resource2', ...], [$data1, $data2, ...]);
$responses = $restMultiClient->delete(['resource1', 'resource2', ...]);
$responses = $restMultiClient->head(['resource1', 'resource2', ...]);
```
