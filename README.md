[![Build Status](https://travis-ci.org/mikecbrant/php-rest-client.svg?branch=master)](https://travis-ci.org/mikecbrant/php-rest-client)
[![Code Climate](https://codeclimate.com/github/mikecbrant/php-rest-client/badges/gpa.svg)](https://codeclimate.com/github/mikecbrant/php-rest-client)
[![Test Coverage](https://codeclimate.com/github/mikecbrant/php-rest-client/badges/coverage.svg)](https://codeclimate.com/github/mikecbrant/php-rest-client/coverage)

# php-rest-client

This library provides classes to implement basic REST clients based on PHP's cURL extension.  Two client classes are made available:

- **RestClient** - a class for executing RESTful service calls using a fluent interface.
- **RestMultiClient** - a class which extends RestClient to provide curl_multi capabilities to allow for multiple REST calls to be made in parallel.
- **CurlHttpResponse** - a class which encapsulates curl response into class wrapper.
- **CurlMultiHttpResponse** - a class which represents a collection of CurlHttpRepsonse objects as returned from multiple parallel REST calls.

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
- PHP HHVM (HipHop)

Please see Travis CI build status at: https://travis-ci.org/mikecbrant/php-rest-client
Please see Code Climate code coverage and health informatoin at: https://codeclimate.com/github/mikecbrant/php-rest-client

This version represents a total re-factoring from previous versions of this library, which were getting long in the tooth and were out of compliance with more modern PHP development standards (i.e. PSR) and tools (i.e. composer).

**Usage example:**

```
// For single requests:
use MikeBrant\RestClientLib;

// instantiate and configure client
$restClient = new RestClient();
$restClient->setRemoteHost('foo.bar.com')
           ->setUriBase('/some_service/')
           ->setUseSsl(true)
           ->setUseSslTestMode(false)
           ->setBasicAuthCredentials('username', 'password')
           ->setHeaders(array('Accept' => 'application/json'));
// make requests against service
$response = $restCLient->get('resource');
$response = $restCLient->post('resource', $data);
$response = $restCLient->put('resource', $data);
$response = $restCLient->delete('resource');
$response = $restCLient->head('resource');

// For multiple parallel requests
$restMultiClient = new RestMultiClient();
$restMultiClient->setRemoteHost('foo.bar.com')
                ->setUriBase('/some_service/')
                ->setUseSsl(true)
                ->setUseSslTestMode(false)
                ->setBasicAuthCredentials('username', 'password')
                ->setHeaders(array('Accept' => 'application/json'));
// make requests against service
$responses = $restCLient->get(['resource1', 'resource2', ...]);
$responses = $restCLient->post(['resource1', 'resource2', ...], [$data1, $data2, ...]);
$responses = $restCLient->put(['resource1', 'resource2', ...], [$data1, $data2, ...]);
$responses = $restCLient->delete(['resource1', 'resource2', ...]);
$responses = $restCLient->head(['resource1', 'resource2', ...]);
```