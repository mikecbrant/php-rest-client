<?php

namespace MikeBrant\RestClientLib;

use RestClient;

/**
* @desc Class which extends RestClient to provide curl_multi capabilities to allow for multiple REST calls to be made in parallel.
*/
class RestMultiClient extends RestClient
{
    
    /**
     * Store array of curl handles for multi_exec
     * 
     * @var array
     */
    protected $curlHandles = array();
    
    /**
     * Stores curl multi handle to which individual handles in curlHandles are added
     * 
     * @var mixed
     */
    protected $curlMultiHandle = null;
    
    /**
     * Variable to store array of URL's used in a curl_multi_exec requests
     * 
     * @var array
     */
    protected $requestUrls = array();
    
    /**
     * Variable to store an array of request headers sent in a multi_exec request
     * 
     * @var array
     */
    protected $requestHeaders = array();
    
    /**
     * Variable to store an array of request data sent for multi_exec POST/PUT requests.
     * 
     * @var array
     */
    protected $requestDataArray = array();
    
   /**
     * Variable to store an array of response codes received for multi_exec requests.
     * 
     * @var array
     */
    protected $responseCodes = array();
    
    /**
     * Variable to store an array of response information received for multi_exec requests.
     * 
     * @var array
     */
    protected $responseInfoArray = array();
    
    /**
     * Variable to store an array of response body content received for multi_exec requests.
     * 
     * @var array
     */
    protected $responseBodies = array();
    
    /**
     * Variable to store the maximum number of handles to be used for curl_multi_exec
     * 
     * @var integer
     */
    protected $maxHandles = 10;
    
    /**
     * Method to perform multiple GET actions using curl_multi_exec.
     * 
     * @param array $actions
     * @param integer $maxHandles
     * @return RestMultiClient
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @throws \LengthException
     */
    public function get(array $actions) {
        $this->validateActionArray($actions);
        
        // set up curl handles
        $this->curlSetup(count($actions));
        $this->setRequestUrls($actions);
        foreach($this->curlHandles as $curl) {
            curl_setopt($curl, CURLOPT_HTTPGET, true); // explicitly set the method to GET    
        }
        $this->curlExec();
        
        return $this;
    }
    
    /**
     * Method to perform multiple POST actions using curl_multi_exec.
     * 
     * @param array $actions
     * @param array $data
     * @return RestMultiClient
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @throws \LengthException
     */
    public function post(array $actions, array $data) {
        $this->validateActionArray($actions);
        $this->validateDataArray($data);
        // verify that the number of data elements matches the number of action elements
        if (count($actions) !== count($data)) {
            throw new \LengthException('The number of actions requested does not match the number of data elements provided.'); 
        }
        
        // set up curl handles
        $this->curlSetup(count($actions));
        $this->setRequestUrls($actions);
        $this->setRequestDataArray($data);
        foreach($this->curlHandles as $curl) {
            curl_setopt($curl, CURLOPT_POST, true); // explicitly set the method to POST 
        }
        $this->curlExec();
        
        return $this;
    }
    
    /**
     * Method to perform multiple PUT actions using curl_multi_exec.
     * 
     * @param array $actions
     * @param array $data
     * @return RestMultiClient
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @throws \LengthException
     */
    public function put(array $actions, array $data) {
        $this->validateActionArray($actions);
        $this->validateDataArray($data);
        // verify that the number of data elements matches the number of action elements
        if (count($actions) !== count($data)) {
            throw new \LengthException('The number of actions requested does not match the number of data elements provided.'); 
        }
        
        // set up curl handles
        $this->curlSetup(count($actions));
        $this->setRequestUrls($actions);
        $this->setRequestDataArray($data);
        foreach($this->curlHandles as $curl) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT'); // explicitly set the method to PUT 
        }
        $this->curlExec();
        
        return $this;
    }
    
    /**
     * Method to perform multiple DELETE actions using curl_multi_exec.
     * 
     * @param array $actions
     * @param integer $maxHandles
     * @return RestMultiClient
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @throws \LengthException
     */
    public function delete(array $actions) {
        $this->validateActionArray($actions);
        
        // set up curl handles
        $this->curlSetup(count($actions));
        $this->setRequestUrls($actions);
        foreach($this->curlHandles as $curl) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE'); // explicitly set the method to DELETE
        }
        $this->curlExec();
        
        return $this;
    }
    
    /**
     * Method to perform multiple HEAD actions using curl_multi_exec.
     * 
     * @param array $actions
     * @return RestMultiClient
     * @throws \Exception
     * @throws \InvalidArgumentException
     * @throws \LengthException
     */
    public function head(array $actions) {
        $this->validateActionArray($actions);
        
        // set up curl handles
        $this->curlSetup(count($actions));
        $this->setRequestUrls($actions);
        foreach($this->curlHandles as $curl) {
            curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'HEAD');
            curl_setopt($curl, CURLOPT_NOBODY, true);
        }
        $this->curlExec();
        
        return $this;
    }
    
    /**
     * Sets maximum number of handles that will be instantiated for curl_multi_exec calls
     * 
     * @param integer $maxHandles
     * @return RestMultiClient
     * @throws \InvalidArgumentException
     */
    public function setMaxHandles($maxHandles = null) {
        if (!is_integer($maxHandles) || $maxHandles <= 0) {
            throw new \InvalidArgumentException('A non-integer value was passed for max_handles parameter.');     
        }
        $this->maxHandles = $maxHandles;
        
        return $this;
    }
    
        /**
     * Returns array of URL's used for last multi_exec requests
     * 
     * @return array
     */
    public function getRequestUrls() {
        return $this->requestUrls;
    }
    
    /**
     * Returns array of URL's data sent with last multi_exec POST/PUT requests
     * 
     * @return array
     */
    public function getRequestDataArray() {
        return $this->requestDataArray;
    }
    
    /**
     * Returns array of headers sent with last multi_exec requests
     * 
     * @return array
     */
    public function getRequestHeaders() {
        return $this->requestHeaders;
    }
    
    /**
     * Returns array of response codes for last multi_exec requests
     * 
     * @return array
     */
    public function getResponseCodes() {
        return $this->responseCodes;
    }
    
    /**
     * Returns array of response info for last multi_exec requests
     * 
     * @return array
     */
    public function getResponseInfoArray() {
        return $this->responseInfoArray;
    }
    
    /**
     * Returns array of response bodies for last multi_exec requests
     * 
     * @return array
     */
    public function getResponseBodies() {
        return $this->responseBodies;
    }
    
        /**
     * Method to set up a given number of curl handles for use with curl_multi_exec
     * 
     * @param integer $handlesNeeded
     * @return void
     * @throws \Exception
     */
    protected function curlSetup($handlesNeeded) {
        $multiCurl = curl_multi_init();
        if($multiCurl === false) {
            throw new \Exception('multi_curl handle failed to initialize.');
        }
        $this->curlMultiHandle = $multiCurl;
        
        for($i = 0; $i < $handlesNeeded; $i++) {
            $curl = $this->curlInit();
            $this->curlHandles[$i] = $curl;
            curl_multi_add_handle($this->curlMultiHandle, $curl);
        }
    }
    
    /**
     * Method to reset the curlMultiHandle and all individual curlHandles related to it.
     * 
     * @return void
     */
    protected function curlTeardown() {
        foreach ($this->curlHandles as $curl) {
            curl_multi_remove_handle($this->curlMultiHandle, $curl);
            $this->curlClose($curl);
        }
        curl_multi_close($this->curlMultiHandle);
        $this->curlHandles = array();
        $this->curlMultiHandle = null;
    }
    
    /**
     * Method to execute curl_multi call
     * 
     * @return void
     * @throws \Exception
     */
    protected function curlExec() {
        // start multi_exec execution
        do {
            $status = curl_multi_exec($this->curlMultiHandle, $active);
        } while ($status === CURLM_CALL_MULTI_PERFORM || $active);
        
        // see if there are any errors on the multi_exec call as a whole
        if($status !== CURLM_OK) {
            throw new \Exception('curl_multi_exec failed with status "' . $status . '"');
        }
        
        // process the results. Note there could be individual errors on specific calls
        foreach($this->curlHandles as $i => $curl) {
            $curl_info = curl_getinfo($curl);
            $this->responseInfoArray[$i] = $curl_info;
            $this->requestHeaders[$i] = $this->responseInfoArray[$i]['request_header'];
            $this->responseCodes[$i] = $this->responseInfoArray[$i]['http_code'];
            $this->responseBodies[$i] = curl_multi_getcontent($curl);
        }
        $this->curlTeardown();
    }
    
    /**
     * Method to reset all properties specific to a particular request/response sequence.
     * 
     * @return void
     */
    protected function resetRequestResponseProperties() {
        $this->requestUrls = array();
        $this->requestHeaders = array();
        $this->requestDataArray = array();
        $this->responseCodes = array();
        $this->responseInfoArray = array();
        $this->responseBodies = array();
    }
    
    /**
     * Method to set the urls for  multi_exec action
     * 
     * @param array $actions
     * @return void
     */
    protected function setRequestUrls(array $actions) {
        for ($i = 0; $i < count($actions); $i++) {
            $url = $this->buildUrl($actions[$i]);
            $this->requestUrls[$i] = $url;
            curl_setopt($this->curlHandles[$i], CURLOPT_URL, $url);
        }   
    }
    
    /**
     * Method to set array of data to be sent along with multi_exec POST/PUT requests
     * 
     * @param array $data
     * @return void
     */
    protected function setRequestDataArray(array $data) {
        for ($i = 0; $i < count($data); $i++) {
            $data = $data[$i];
            $this->requestDataArray[$i] = $data;
            curl_setopt($this->curlHandles[$i], CURLOPT_POSTFIELDS, $data);
        }
    }
    
    /**
     * Method to provide common validation for action array parameters
     * 
     * @param array $actions
     * @return void
     * @throws \InvalidArgumentException
     * @throws \LengthException
     */
    protected function validateActionArray(array $actions) {
        if(empty($actions)) {
            throw new \InvalidArgumentException('An empty array was passed for actions parameter.');
        }
        if(count($actions) > $this->maxHandles) {
            throw new \LengthException('Length of actions array exceeds maxHandles setting.');
        }
        foreach($actions as $action) {
            $this->validateActionArray($action);
        }
    }
    
    /**
     * Method to provide common validation for data array parameters
     * 
     * @param array $data
     * @return void
     * @throws \InvalidArgumentException
     * @throws \LengthException
     */
    protected function validateDataArray(array $data) {
        if(empty($data)) {
            throw new \InvalidArgumentException('An empty array was passed for data parameter');
        }
        if(count($data) > $this->maxHandles) {
            throw new \LengthException('Length of data array exceeds maxHandles setting.');
        }
        foreach($data as $item) {
            $this->validateData($item);
        }
    }
}