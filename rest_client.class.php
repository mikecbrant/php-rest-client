<?php

/**
* @desc Class to implement a basic REST client based on cURL. Support for HTTP Basic Authentication is provided. This class also allows use of HTTPS/SSL with ability to toggle SSL peer certificate validate to allow for use in "test mode" where server may not have a commericial SSL cert. Support for curl_multi_exec functions is provided to enable multiple requests to be made in parallel. Requires PHP cURL support to be enabled. Public methods with the exception of get_* methods return the instance itself for use in fluid/chained progamming style.
* 
* Usage example:
* 
* $rest_client = new rest_client();
* $rest_client->set_remote_host('foo.bar.com')              [Host name must be set]
*   ->set_uri_base('/some_service/')                        [Optional, default is '/']
*   ->set_use_ssl(true)                                     [Optional, default is false]
*   ->set_use_ssl_test_mode(false)                          [Optional, default is false]
*   ->set_basic_auth_credentials('username', 'password')    [Optional]
*   ->set_headers(array('Accept' => 'application/json'))    [Optional, if not specified, default cURL headers for each request type will be used]
*   ->get('resource')                                       [Perform HTTP GET on URL [hostname].[uri_base].[resource parameter passed to method]]
*   ->post('resource', [data])                              [Perform HTTP POST on passed resource reference, data can be in form allowed by curl_setopt CURLOPT_POSTFIELDS]
*   ->put('resource', [data])                               [Perform HTTP PUT on passed resource reference, data can be in form allowed by curl_setopt CURLOPT_POSTFIELDS]
*   ->delete('resource');                                   [Perform HTTP DELETE on passed resource reference]
* 
* @author Mike Brant
* @version 1 2012-03-13
* @version 2 2012-05-04
*/

/**
* @desc Class to implement a basic REST client based on cURL
*/
class rest_client
{
    /**
    * Flag to determine if basic authentication is to be used.
    * 
    * @var boolean
    */
    protected $use_basic_auth = false;

    /**
    * User Name for HTTP Basic Auth
    * 
    * @var string
    */
    protected $basic_auth_username = NULL;

    /**
    * Boomerang password for HTTP Basic Auth
    *
    * @var string
    */
    protected $basic_auth_password = NULL;
    
    /**
    * Flag to determine if SSL is used
    * 
    * @var boolean
    */
    protected $use_ssl = false;
   
    /**
    * Flag to determine is we are to run in test mode where host's SSL cert is not verified
    * 
    * @var boolean
    */
    protected $use_ssl_test_mode = false;

    /**
    * Array containing headers to be used for request
    * 
    * @var array
    */
    protected $headers = array();
    
    /**
    * Integer value representing number of seconds to set for cURL timeout option. Defaults to 30 seconds.
    * 
    * @var integer
    */
    protected $timeout = 30;

    /**
    * Stores curl handle
    *
    * @var mixed
    */
    protected $curl = NULL;
    
    /**
    * Store array of curl handles for multi_exec
    * 
    * @var array
    */
    protected $curl_multi_handle_array = array();
    
    /**
    * Stores curl multi handle to which individual handles in curl_multi_handle_array are added
    * 
    * @var mixed
    */
    protected $curl_multi_handle = NULL;
    
    /**
    * Variable to store remote host name
    * 
    * @var string
    */
    protected $remote_host = NULL;
    
    /**
    * Variable which can hold a URI base for all actions
    * 
    * @var string
    */
    protected $uri_base = '/';
    
    /**
    * Variable to store request URL that is formed before a request is made
    * 
    * @var string
    */
    protected $request_url = NULL;
    
    /**
    * Variable to store array of URL's used in a curl_multi_exec requests
    * 
    * @var array
    */
    protected $multi_request_urls = array();
    
    /**
    * Variable to store the request header as sent
    * 
    * @var string
    */
    protected $request_header = NULL;
    
    /**
    * Variable to store an array of request headers sent in a multi_exec request
    * 
    * @var array
    */
    protected $multi_request_headers = array();
    
    /**
    * Variable to store the request data sent for POST/PUT requests. THis could be array, string, etc.
    * 
    * @var mixed
    */
    protected $request_data = NULL;
    
    /**
    * Variable to store an array of request data sent for multi_exec POST/PUT requests.
    * 
    * @var array
    */
    protected $multi_request_data = array();
    
    /**
    * Variable to store response code
    * 
    * @var integer
    */
    protected $response_code = NULL;
    
    /**
    * Variable to store an array of response codes received for multi_exec requests.
    * 
    * @var array
    */
    protected $multi_response_codes = array();
    
    /**
    * Variable to store cURL response info array
    * 
    * @var array
    */
    protected $response_info = NULL;
    
    /**
    * Variable to store an array of response information received for multi_exec requests.
    * 
    * @var array
    */
    protected $multi_response_info = array();
    
    /**
    * Variable to store cURL reponse body
    * 
    * @var string
    */
    protected $response_body = NULL;
    
    /**
    * Variable to store an array of response body content received for multi_exec requests.
    * 
    * @var array
    */
    protected $multi_response_bodies = array();
    
    /**
    * Variable to store the maximum number of handles to be used for curl_multi_exec
    * 
    * @var integer
    */
    protected $max_multi_exec_handles = 10;

    /**
    * Constructor method.
    *
    * @return void
    */
    public function __construct() {

    }
    
    /**
    * Method to initialize to set cURL handle in object
    * 
    * @return void
    * @throws Exception
    */
    protected function _curl_setup() {        
        // reset all request/response properties
        $this->_reset_request_response_properties();
        
        // initialize cURL
        $this->curl = $this->_curl_init();
    }
    
    /**
    * Method to set up a given number of curl handles for use with curl_multi_exec
    * 
    * @param integer $handles_needed
    * @return void
    * @throws Exception
    */
    protected function _curl_multi_setup($handles_needed = NULL) {
        if(!is_integer($handles_needed)) {
            throw new Exception('Non-integer value passed for handles_needed parameter - ' . __METHOD__ . ' Line ' . __LINE__);
        } else if ($handles_needed <= 0) {
            throw new Exception('A positive integer value must be specified for handles_needed parameter - ' . __METHOD__ . ' Line ' . __LINE__);
        }
        
        // verify that the number of handles requested does not exceed the max number of handles
        if ($handles_needed > $this->max_multi_exec_handles) {
            throw new Exception('The number of handles requested exceeds maximum allowed number of handles - ' . __METHOD__ . ' Line ' . __LINE__); 
        }
        
        $this->curl_multi_handle = curl_multi_init();
        
        for($i = 0; $i < $handles_needed; $i++) {
            $curl = $this->_curl_init();
            $this->curl_multi_handle_array[$i] = $curl;
            curl_multi_add_handle($this->curl_multi_handle, $curl);
        }
    }
    
    /**
    * Method to initilize a cURL handle
    * 
    * @return resource
    * @throws Exception
    */
    protected function _curl_init() {
        // initialize cURL
        $curl = curl_init();
        if($curl === false) {
            throw new Exception('cURL failed to initialize - ' . __METHOD__ . ' Line ' . __LINE__);
        } else {
            // set timeout
            curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
            
            if (true === $this->use_basic_auth) {
                // set basic HTTP authentication settings
                curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($curl, CURLOPT_USERPWD, $this->basic_auth_username . ':' . $this->basic_auth_password);
            }
            
            if (!empty($this->headers)) {
                // set headers
                $headers = array();
                foreach ($this->headers as $key=>$val) {
                    $headers[] = $key . ': ' . $val;
                }
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            }
            
            if (true === $this->use_ssl && true === $this->use_ssl_test_mode) {
                // if not in production environment, we want to ignore SSL validation
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            }
            
            // set option to add request header information to curl_getinfo output
            curl_setopt($curl, CURLINFO_HEADER_OUT, true);

            // set option to return content body
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            
            return $curl;
        }    
    }

    /**
    * Method to reset curl handle
    * 
    * @return void
    */
    protected function _curl_teardown() {
        $this->_curl_close($this->curl);
        $this->curl = NULL;
    }
    
    /**
    * Method to reset the curl_multi_handle
    * 
    * @return void
    */
    protected function _curl_multi_teardown() {
        foreach ($this->curl_multi_handle_array as $curl) {
            curl_multi_remove_handle($this->curl_multi_handle, $curl);
            $this->_curl_close($curl);
        }
        curl_multi_close($this->curl_multi_handle);
        $this->curl_multi_handle_array = array();
        $this->curl_multi_handle = NULL;
    }
    
    /**
    * Method to close cURL handle
    * 
    * @return void
    */
    protected function _curl_close($curl = NULL) {
        if(!is_resource($curl)) {
            throw new Exception('cURL resource not passed as curl parameter - ' . __METHOD__ . ' Line ' . __LINE__);    
        }
        curl_close($curl);
    }
    
    /**
    * Method to execute cURL call
    * 
    * @return void
    * @throws Exception
    */
    protected function _curl_exec() {
        $curl_result = curl_exec($this->curl);
        if($curl_result === false) {
            // our cURL call failed for some reason
            $curl_error = curl_error($this->curl);
            $this->_curl_teardown();
            throw new Exception('cURL call failed with message "' . $curl_error . '"  - ' . __METHOD__ . ' Line ' . __LINE__);
        } else {
            // set object properties for request/response
            $curl_info = curl_getinfo($this->curl);
            $this->response_info = $curl_info;
            $this->request_header = $this->response_info['request_header'];
            $this->response_code = $this->response_info['http_code'];
            $this->response_body = $curl_result;
            $this->_curl_teardown();
        }
    }
    
    /**
    * Method to execute execute curl_multi call
    * 
    * @return void
    * @throws Exception
    */
    protected function _curl_multi_exec() {
        // start multi_exec execution
        do {
            $status = curl_multi_exec($this->curl_multi_handle, $active);
        } while ($status === CURLM_CALL_MULTI_PERFORM || $active);
        
        // see if there are any errors on the multi_exec call as a whole
        if($status != CURLM_OK) {
            throw new Exception('cURL multi_exec failed with error "' . $status . '" - ' . __METHOD__ . ' Line ' . __LINE__);
        }
        
        // process the results. Note there could be individual errors on specific calls
        foreach($this->curl_multi_handle_array as $i => $curl) {
            $curl_info = curl_getinfo($curl);
            $this->multi_response_info[$i] = $curl_info;
            $this->multi_request_headers[$i] = $this->multi_response_info[$i]['request_header'];
            $this->multi_response_codes[$i] = $this->multi_response_info[$i]['http_code'];
            $this->multi_response_bodies[$i] = curl_multi_getcontent($curl);
        }
        $this->_curl_multi_teardown();    
    }
    
    /**
    * Method to reset all properties specific to a particular request/response sequence.
    * 
    * @return void
    */
    protected function _reset_request_response_properties() {
        $this->request_url = NULL;
        $this->request_header = NULL;
        $this->request_data = NULL;
        $this->response_code = NULL;
        $this->response_info = NULL;
        $this->response_body = NULL;
        $this->multi_request_urls = array();
        $this->multi_request_headers = array();
        $this->multi_request_data = array();
        $this->multi_response_codes = array();
        $this->multi_response_info = array();
        $this->multi_response_bodies = array();
    }
    
    /**
    * Method to set the url for request
    * 
    * @param string $action
    * @return void
    * @throws Exception
    */
    protected function _set_request_url($action = NULL) {
        if (!is_string($action)) {
            throw new Exception('Non-string value passed as parameter - ' . __METHOD__ . ' Line ' . __LINE__); 
        }
        
        $this->request_url = $this->_curl_set_url($action, $this->curl);
    }
    
    /**
    * Method to set the urls for  multi_exec action
    * 
    * @param array $actions
    * @return void
    * @throws Exception
    */
    protected function _set_multi_request_urls($actions = NULL) {
        if (!is_array($actions)) {
            throw new Exception('A non-array value was passed for actions parameter - ' . __METHOD__ . ' Line ' . __LINE__);
        }
        
        for ($i = 0; $i < count($actions); $i++) {
            $this->multi_request_urls[$i] = $this->_curl_set_url($actions[$i], $this->curl_multi_handle_array[$i]);
        }   
    }
    
    /**
    * Method to set a URL on a cURL handle.
    * 
    * @param string $action
    * @return void
    * @throws Exception
    */
    protected function _curl_set_url($action = NULL, $curl = NULL) {
        if (!is_string($action)) {
            throw new Exception('Non-string value passed as parameter - ' . __METHOD__ . ' Line ' . __LINE__); 
        }
        if(!is_resource($curl)) {
            throw new Exception('cURL resource not passed as curl parameter - ' . __METHOD__ . ' Line ' . __LINE__);    
        }
        if (empty($this->remote_host)) {
            throw new Exception('Remote host not set - ' . __METHOD__ . ' Line ' . __LINE__);
        }
        
        if (true === $this->use_ssl) {
            $url = 'https://';
        } else {
            $url = 'http://';
        }
        
        $url = $url . $this->remote_host . $this->uri_base . $action;

        curl_setopt($curl, CURLOPT_URL, $url);
        
        return $url;
    }
    
    /**
    * Method to set data to be sent along with POST/PUT requests
    * 
    * @param mixed $data
    * @return void
    * @throws Exception
    */
    protected function _set_request_data($data = NULL) {
        if (is_null($data)) {
            throw new Exception('Nothing passed for data parameter - ' . __METHOD__ . ' Line ' . __LINE__);
        }
        $this->request_data = $data;
        $this->_curl_set_request_data($data, $this->curl);
    }
    
    /**
    * Method to set array of data to be sent along with multi_exec POST/PUT requests
    * 
    * @param array $data
    * @return void
    * @throws Exception
    */
    protected function _set_multi_request_data($data = NULL) {
        if (!is_array($data)) {
            throw new Exception('Non-array passed for data parameter - ' . __METHOD__ . ' Line ' . __LINE__);
        }
        
        for ($i = 0; $i < count($actions); $i++) {
            $this->multi_request_data[$i] = $data;
            $this->_curl_set_request_data($data[$i], $this->curl_multi_handle_array[$i]);
        }
    }
    
    /**
    * Method to set data for POST/PUT on given curl handle
    * 
    * @param mixed $data
    * @param resource $curl
    * @return void
    * @throws Exception
    */
    protected function _curl_set_request_data($data = NULL, $curl = NULL) {
        if (is_null($data)) {
            throw new Exception('Nothing passed as data parameter - ' . __METHOD__ . ' Line ' . __LINE__); 
        }
        if(!is_resource($curl)) {
            throw new Exception('cURL resource not passed as curl parameter - ' . __METHOD__ . ' Line ' . __LINE__);    
        }
        
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }

    /**
    * Sets host name of remote server
    * 
    * @param string $host
    * @return rest_client
    * @throws Exception
    */
    public function set_remote_host($host = NULL) {
        if(empty($host)) {
            throw new Exception('Host name not provided - ' . __METHOD__ . ' Line ' . __LINE__);
        } else if(!is_string($host)) {
            throw new Exception('Non-string host name provided - ' . __METHOD__ . ' Line ' . __LINE__);
        }
        
        // remove any http(s):// at beginning of host name
        $https_pattern = '#https//:#i';
        $http_pattern = '#http//:#i';
        if (1 === preg_match($https_pattern, $host)) {
            // this needs to be SSL request
            $this->set_use_ssl(true);
            $host = str_ireplace('https://', '', $host);
        } else if (1 === preg_match($http_pattern, $host)) {
            $host = str_ireplace('http://', '', $host);
        }
        
        // remove trailing slash in host name
        $host = rtrim($host, '/');
        
        // look for common SSL port values in host name to see if SSL is needed
        $port_patterns = array(
            '/:443$/',
            '/:8443$/',
        );
        foreach ($port_patterns as $pattern) {
            if (1 === preg_match($pattern, $host)) {
                $this->set_use_ssl(true);
            }
        }
        
        $this->remote_host = $host;
        
        return $this;
    }
    
    /**
    * Sets URI base for the instance
    * 
    * @param string $uri_base
    * @return rest_client
    * @throws Exception
    */
    public function set_uri_base($uri_base = NULL) {
        if (!is_string($uri_base)) {
            throw new Exception('Non-string value passed as parameter - ' . __METHOD__ . ' Line ' . __LINE__); 
        }
        // make sure we always have forward slash at beginning and end of uri_base
        $uri_base = '/' . ltrim($uri_base, '/');
        $uri_base = rtrim($uri_base, '/') . '/';
        
        $this->uri_base = $uri_base;
        
        return $this;
    }
    
    /**
    * Sets whether SSL is to be used
    * 
    * @param boolean $value
    * @return rest_client
    * @throws Exception
    */
    public function set_use_ssl($value = NULL) {
        if (!is_bool($value)) {
            throw new Exception('Non-boolean value passed as parameter - ' . __METHOD__ . ' Line ' . __LINE__);    
        }
        $this->use_ssl = $value;
        
        return $this;
    }
    
    /**
    * Sets whether SSL Test Mode is to be used
    * 
    * @param boolean $value
    * @return rest_client
    * @throws Exception
    */
    public function set_use_ssl_test_mode($value = NULL) {
        if (!is_bool($value)) {
            throw new Exception('Non-boolean value passed as parameter - ' . __METHOD__ . ' Line ' . __LINE__);    
        }
        $this->use_ssl_test_mode = $value;
        
        return $this;
    }
    /**
    * Sets basic authentication credentials
    * 
    * @param string $user
    * @param string $password
    * @return rest_client
    * @throws Exception
    */
    public function set_basic_auth_credentials($user = NULL, $password = NULL) {
        if (empty($user)) {
            throw new Exception('User name not provided when trying to set basic authentication credentials - ' . __METHOD__ . ' Line ' . __LINE__);    
        }
        
        if (empty($password)) {
            throw new Exception('Password not provided when trying to set basic authentication credentials - ' . __METHOD__ . ' Line ' . __LINE__);    
        }
        
        $this->use_basic_auth = true;
        $this->basic_auth_username = $user;
        $this->basic_auth_password = $password;
        
        return $this;
    }

    /**
    * Sets HTTP headers from an associative array where key is header name and value is the header value
    * 
    * @param array $headers
    * @return rest_client
    * @throws Exception
    */
    public function set_headers(array $headers = array()) {
        if (!is_array($headers)) {
            throw new Exception('Non-array passed when trying to set headers - ' . __METHOD__ . ' Line ' . __LINE__);   
        }
        foreach ($headers as $key=>$val) {
            $this->headers[$key] = $val;
        }
        
        return $this;  
    }
    
    /**
    * Sets maximum timeout for cURL requests
    * 
    * @param integer $seconds
    * @return rest_client
    * @throws Exception
    */
    public function set_timeout($seconds = NULL) {
        if(!is_integer($seconds)) {
            throw new Exception('Non-integer passed when trying to set timeout - ' . __METHOD__ . ' Line ' . __LINE__);
        }
        $this->timeout = $seconds;
        
        return $this;
    }
    
    /**
    * Sets maximum number of handles that will be instantiated for curl_multi_exec calls
    * 
    * @param integer $max_handles
    * @return rest_client
    * @throws Exception
    */
    public function set_max_multi_exec_handles($max_handles = NULL) {
        if (!is_integer($max_handles)) {
            throw new Exception('A non-integer value was passed for max_handles parameter - ' . __METHOD__ . ' Line ' . __LINE__);     
        } else if ($max_handles <= 0) {
            throw new Exception('A positive integer value must be specified for max_handles parameter - ' . __METHOD__ . ' Line ' . __LINE__);
        } else {
            $this->max_multi_exec_handles = $max_handles;
        }
        
        return $this;    
    }
    
    /**
    * Method to execute GET on server
    * 
    * @param string $action
    * @return rest_client
    * @throws Exception
    */
    public function get($action = NULL) {
        if (!is_string($action)) {
            throw new Exception('A non-string value was passed for action parameter - ' . __METHOD__ . ' Line ' . __LINE__);
        }
        $this->_curl_setup();
        $this->_set_request_url($action);
        curl_setopt($this->curl, CURLOPT_HTTPGET, true); // explicitly set the method to GET
        $this->_curl_exec();
        
        return $this;
    }
    
    /**
    * Method to perform multiple GET actions using curl_multi_exec. The max_handles parameter is optional and can be used to change the default maximum number of allowable multi_exec handles.
    * 
    * @param array $actions
    * @param integer $max_handles
    * @return rest_client
    * @throws Exception
    */
    public function multi_get($actions = NULL, $max_handles = NULL) {
        if (!is_array($actions)) {
            throw new Exception('A non-array value was passed for actions parameter - ' . __METHOD__ . ' Line ' . __LINE__);
        }
        if (!is_null($max_handles)) {
            $this->set_max_multi_exec_handles($max_handles);
        }
        
        $handles_needed = count($actions);

        // verify that the number of handles requested does not exceed the max number of handles
        if ($handles_needed > $this->max_multi_exec_handles) {
            throw new Exception('The number of handles requested exceeds maximum allowed number of handles - ' . __METHOD__ . ' Line ' . __LINE__); 
        }
        
        // set up curl handles
        $this->_curl_multi_setup($handles_needed);
        $this->_set_multi_request_urls($actions);
        foreach($this->curl_multi_handle_array as $curl) {
            curl_setopt($curl, CURLOPT_HTTPGET, true); // explicitly set the method to GET    
        }
        $this->_curl_multi_exec();
        
        return $this;
    }
    
    /**
    * Method to exexute POST on server
    * 
    * @param mixed $action
    * @param mixed $data
    * @return rest_client
    * @throws Exception
    */
    public function post($action = NULL, $data = NULL) {
        if (!is_string($action)) {
            throw new Exception('A non-string value was passed for action parameter - ' . __METHOD__ . ' Line ' . __LINE__);
        }
        if (is_null($data)) {
            throw new Exception('Nothing passed for data parameter - ' . __METHOD__ . ' Line ' . __LINE__);
        }
        $this->_curl_setup();
        $this->_set_request_url($action);
        $this->_set_request_data($data);
        curl_setopt($this->curl, CURLOPT_POST, true); // explicitly set the method to POST
        $this->_curl_exec();
        
        return $this;
    }
    
    /**
    * Method to perform multiple POST actions using curl_multi_exec. The max_handles parameter is optional and can be used to change the default maximum number of allowable multi_exec handles.
    * 
    * @param array $actions
    * @param array $data
    * @param integer $max_handles
    * @return rest_client
    * @throws Exception
    */
    public function multi_post($actions = NULL, $data = NULL, $max_handles = NULL) {
        if (!is_array($actions)) {
            throw new Exception('A non-array value was passed for actions parameter - ' . __METHOD__ . ' Line ' . __LINE__);
        }
        if (!is_array($data)) {
            throw new Exception('A non-array value was passed for data parameter - ' . __METHOD__ . ' Line ' . __LINE__);
        }
        if (!is_null($max_handles)) {
            $this->set_max_multi_exec_handles($max_handles);
        }
        
        $handles_needed = count($actions);
        $data_count = count($data);

        // verify that the number of handles requested does not exceed the max number of handles
        if ($handles_needed > $this->max_multi_exec_handles) {
            throw new Exception('The number of handles requested exceeds maximum allowed number of handles - ' . __METHOD__ . ' Line ' . __LINE__); 
        }
        
        // verify that the number of data elements matches the number of action elements
        if ($handles_needed !== $data_count) {
            throw new Exception('The number of actions requested does not match the number of data elements provided - ' . __METHOD__ . ' Line ' . __LINE__); 
        } 
        
        // set up curl handles
        $this->_curl_multi_setup($handles_needed);
        $this->_set_multi_request_urls($actions);
        $this->_set_multi_request_data($data);
        foreach($this->curl_multi_handle_array as $curl) {
            curl_setopt($curl, CURLOPT_POST, true); // explicitly set the method to POST 
        }
        $this->_curl_multi_exec();
        
        return $this;
    }
    
    /**
    * Method to execute PUT on server
    * 
    * @param string $action
    * @param mixed $data
    * @return rest_client
    * @throws Exception
    */
    public function put($action = NULL, $data = NULL) {
        if (!is_string($action)) {
            throw new Exception('A non-string value was passed for action parameter - ' . __METHOD__ . ' Line ' . __LINE__);
        }
        if (is_null($data)) {
            throw new Exception('Nothing passed for data parameter - ' . __METHOD__ . ' Line ' . __LINE__);
        }
        $this->_curl_setup();
        $this->_set_request_url($action);
        $this->_set_request_data($data);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'PUT'); // explicitly set the method to PUT
        $this->_curl_exec();
        
        return $this;
    }
    
    /**
    * Method to perform multiple PUT actions using curl_multi_exec. The max_handles parameter is optional and can be used to change the default maximum number of allowable multi_exec handles.
    * 
    * @param array $actions
    * @param array $data
    * @param integer $max_handles
    * @return rest_client
    * @throws Exception
    */
    public function multi_put($actions = NULL, $data = NULL, $max_handles = NULL) {
        if (!is_array($actions)) {
            throw new Exception('A non-array value was passed for actions parameter - ' . __METHOD__ . ' Line ' . __LINE__);
        }
        if (!is_array($data)) {
            throw new Exception('A non-array value was passed for data parameter - ' . __METHOD__ . ' Line ' . __LINE__);
        }
        if (!is_null($max_handles)) {
            $this->set_max_multi_exec_handles($max_handles);
        }
        
        $handles_needed = count($actions);
        $data_count = count($data);

        // verify that the number of handles requested does not exceed the max number of handles
        if ($handles_needed > $this->max_multi_exec_handles) {
            throw new Exception('The number of handles requested exceeds maximum allowed number of handles - ' . __METHOD__ . ' Line ' . __LINE__); 
        }
        
        // verify that the number of data elements matches the number of action elements
        if ($handles_needed !== $data_count) {
            throw new Exception('The number of actions requested does not match the number of data elements provided - ' . __METHOD__ . ' Line ' . __LINE__); 
        } 
        
        // set up curl handles
        $this->_curl_multi_setup($handles_needed);
        $this->_set_multi_request_urls($actions);
        $this->_set_multi_request_data($data);
        foreach($this->curl_multi_handle_array as $curl) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT'); // explicitly set the method to PUT 
        }
        $this->_curl_multi_exec();
        
        return $this;
    }
    
    /**
    * Method to execute DELETE on server
    * 
    * @param string $action
    * @return rest_client
    * @throws Exception
    */
    public function delete($action = NULL) {
        if (!is_string($action)) {
            throw new Exception('Nothing passed for data parameter - ' . __METHOD__ . ' Line ' . __LINE__);
        }
        $this->_curl_setup();
        $this->_set_request_url($action);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'DELETE'); // explicitly set the method to DELETE
        $this->_curl_exec();
        
        return $this;
    }
    
    /**
    * Method to perform multiple DELETE actions using curl_multi_exec. The max_handles parameter is optional and can be used to change the default maximum number of allowable multi_exec handles.
    * 
    * @param array $actions
    * @param integer $max_handles
    * @return rest_client
    * @throws Exception
    */
    public function multi_delete($actions = NULL, $max_handles = NULL) {
        if (!is_array($actions)) {
            throw new Exception('A non-array value was passed for actions parameter - ' . __METHOD__ . ' Line ' . __LINE__);
        }

        if (!is_null($max_handles)) {
            $this->set_max_multi_exec_handles($max_handles);
        }
        
        $handles_needed = count($actions);

        // verify that the number of handles requested does not exceed the max number of handles
        if ($handles_needed > $this->max_multi_exec_handles) {
            throw new Exception('The number of handles requested exceeds maximum allowed number of handles - ' . __METHOD__ . ' Line ' . __LINE__); 
        }
        
        // set up curl handles
        $this->_curl_multi_setup($handles_needed);
        $this->_set_multi_request_urls($actions);
        foreach($this->curl_multi_handle_array as $curl) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE'); // explicitly set the method to DELETE
        }
        $this->_curl_multi_exec();
        
        return $this;
    }
    
    /**
    * Returns URL used for last request
    * 
    * @return string
    */
    public function get_request_url() {
        return $this->request_url;
    }
    
    
    /**
    * Returns array of URL's used for last multi_exec requests
    * 
    * @return array
    */
    public function get_multi_request_urls() {
        return $this->multi_request_urls;
    }
    
    /**
    * Returns data sent with last request (i.e. POST/PUT data)
    * 
    * @return mixed
    */
    public function get_request_data() {
        return $this->request_data;
    }

    /**
    * Returns array of URL's data sent with last multi_exec POST/PUT requests
    * 
    * @return array
    */
    public function get_multi_request_data() {
        return $this->multi_request_data;
    }
    
    /**
    * Returns request header for last request
    * 
    * @return string
    */
    public function get_request_header() {
        return $this->request_header;
    }
    
    /**
    * Returns array of headers sent with last multi_exec requests
    * 
    * @return array
    */
    public function get_multi_request_headers() {
        return $this->multi_request_headers;
    }
    
    /**
    * Returns reespsone code for last request
    * 
    * @return integer
    */
    public function get_response_code() {
        return $this->response_code;
    }

    /**
    * Returns array of response codes for last multi_exec requests
    * 
    * @return array
    */
    public function get_multi_response_codes() {
        return $this->multi_response_codes;
    }
    
    /**
    * Returns cURL response information array from last request
    * 
    * @return array
    */
    public function get_response_info() {
        return $this->response_info;
    }

    /**
    * Returns array of response info for last multi_exec requests
    * 
    * @return array
    */
    public function get_multi_response_info() {
        return $this->multi_response_info;
    }
    
    /**
    * Returns response body from last request
    * 
    * @return string
    */
    public function get_response_body() {
        return $this->response_body;
    }
    
    /**
    * Returns array of response bodies for last multi_exec requests
    * 
    * @return array
    */
    public function get_multi_response_bodies() {
        return $this->multi_response_bodies;
    }
}
?>