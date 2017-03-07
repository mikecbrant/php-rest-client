<?php

namespace MikeBrant\RestClientLib;

/**
* @desc Class which extendd RestClient to provide curl_multi capabilities, allowing for multiple REST calls to be made in parallel.
*/
class RestMultiClient extends RestClient
{
    /**
     * Store array of curl handles for multi_exec
     * 
     * @var array
     */
    private $curlHandles = array();
    
    /**
     * Stores curl multi handle to which individual handles in curlHandles are added
     * 
     * @var mixed
     */
    private $curlMultiHandle = null;
    
    /**
     * Variable to store the maximum number of handles to be used for curl_multi_exec
     * 
     * @var integer
     */
    private $maxHandles = 10;
    
    /**
     * Variable to store an array of request headers sent in a multi_exec request
     * 
     * @var array
     */
    private $requestHeaders = array();
    
    /**
     * Variable to store an array of request data sent for multi_exec POST/PUT requests.
     * 
     * @var array
     */
    private $requestDataArray = array();
     
    /**
     * Variable to store CurlMultiHttpResponse object
     * 
     * @var CurlMultiHttpResponse
     */
    private $curlMultiHttpResponse = null;
    
    /**
     * Constructor method. Currently there is no instantiation logic.
     * 
     * @return void
     */
    public function __construct() {}
    
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
    public function get($actions) {
        $this->validateActionArray($actions);
        
        // set up curl handles
        $this->curlMultiSetup(count($actions));
        $this->setRequestUrls($actions);
        foreach($this->curlHandles as $curl) {
            curl_setopt($curl, CURLOPT_HTTPGET, true); // explicitly set the method to GET    
        }
        $this->curlMultiExec();
        
        return $this->curlMultiHttpResponse;
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
    public function post($actions, $data) {
        $this->validateActionArray($actions);
        $this->validateDataArray($data);
        // verify that the number of data elements matches the number of action elements
        if (count($actions) !== count($data)) {
            throw new \LengthException('The number of actions requested does not match the number of data elements provided.'); 
        }
        
        // set up curl handles
        $this->curlMultiSetup(count($actions));
        $this->setRequestUrls($actions);
        $this->setRequestDataArray($data);
        foreach($this->curlHandles as $curl) {
            curl_setopt($curl, CURLOPT_POST, true); // explicitly set the method to POST 
        }
        $this->curlMultiExec();
        
        return $this->curlMultiHttpResponse;
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
    public function put($actions, $data) {
        $this->validateActionArray($actions);
        $this->validateDataArray($data);
        // verify that the number of data elements matches the number of action elements
        if (count($actions) !== count($data)) {
            throw new \LengthException('The number of actions requested does not match the number of data elements provided.'); 
        }
        
        // set up curl handles
        $this->curlMultiSetup(count($actions));
        $this->setRequestUrls($actions);
        $this->setRequestDataArray($data);
        foreach($this->curlHandles as $curl) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT'); // explicitly set the method to PUT 
        }
        $this->curlMultiExec();
        
        return $this->curlMultiHttpResponse;
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
    public function delete($actions) {
        $this->validateActionArray($actions);
        
        // set up curl handles
        $this->curlMultiSetup(count($actions));
        $this->setRequestUrls($actions);
        foreach($this->curlHandles as $curl) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE'); // explicitly set the method to DELETE
        }
        $this->curlMultiExec();
        
        return $this->curlMultiHttpResponse;
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
    public function head($actions) {
        $this->validateActionArray($actions);
        
        // set up curl handles
        $this->curlMultiSetup(count($actions));
        $this->setRequestUrls($actions);
        foreach($this->curlHandles as $curl) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'HEAD');
            curl_setopt($curl, CURLOPT_NOBODY, true);
        }
        $this->curlMultiExec();
        
        return $this->curlMultiHttpResponse;
    }
    
    /**
     * Sets maximum number of handles that will be instantiated for curl_multi_exec calls
     * 
     * @param integer $maxHandles
     * @return RestMultiClient
     * @throws \InvalidArgumentException
     */
    public function setMaxHandles($maxHandles) {
        if (!is_integer($maxHandles) || $maxHandles <= 0) {
            throw new \InvalidArgumentException('A non-integer value was passed for max_handles parameter.');     
        }
        $this->maxHandles = $maxHandles;
        
        return $this->curlMultiHttpResponse;
    }
    
    /**
     * Getter for maxHandles setting
     * 
     * @return integer
     */
    public function getMaxHandles() {
        return $this->maxHandles;
    }
    
    /**
     * Method to set up a given number of curl handles for use with curl_multi_exec
     * 
     * @param integer $handlesNeeded
     * @return void
     * @throws \Exception
     */
    private function curlMultiSetup($handlesNeeded) {
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
    private function curlMultiTeardown() {
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
    private function curlMultiExec() {
        // start multi_exec execution
        do {
            $status = curl_multi_exec($this->curlMultiHandle, $active);
        } while ($status === CURLM_CALL_MULTI_PERFORM || $active);
        
        // see if there are any errors on the multi_exec call as a whole
        if($status !== CURLM_OK) {
            throw new \Exception('curl_multi_exec failed with status "' . $status . '"');
        }
        
        // process the results. Note there could be individual errors on specific calls
        $this->curlMultiHttpResponse = new CurlMultiHttpResponse();
        foreach($this->curlHandles as $curl) {
            try {
                $response = new CurlHttpResponse(
                    curl_multi_getcontent($curl),
                    curl_getinfo($curl)
                );
            } catch (\InvalidArgumentException $e) {
                $this->curlMultiTeardown();
                throw new \Exception(
                   'Unable to instantiate CurlHttpResponse. Message: "' . $e->getMessage() . '"',
                   $e->getCode(),
                   $e
                );
            }
            $this->curlMultiHttpResponse->addResponse($response);
        }
        $this->curlMultiTeardown();
    }
    
    /**
     * Method to reset all properties specific to a particular request/response sequence.
     * 
     * @return void
     */
    protected function resetRequestResponseProperties() {
        $this->curlMultiHttpResponse = null;
        $this->requestHeaders = array();
        $this->requestDataArray = array();
    }
    
    /**
     * Method to set the urls for  multi_exec action
     * 
     * @param array $actions
     * @return void
     */
    private function setRequestUrls(array $actions) {
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
    private function setRequestDataArray(array $data) {
        for ($i = 0; $i < count($data); $i++) {
            $element = $data[$i];
            $this->requestDataArray[$i] = $element;
            curl_setopt($this->curlHandles[$i], CURLOPT_POSTFIELDS, $element);
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
    private function validateActionArray(array $actions) {
        if(empty($actions)) {
            throw new \InvalidArgumentException('An empty array was passed for actions parameter.');
        }
        if(count($actions) > $this->maxHandles) {
            throw new \LengthException('Length of actions array exceeds maxHandles setting.');
        }
        foreach($actions as $action) {
            $this->validateAction($action);
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
    private function validateDataArray(array $data) {
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
