<?php
/**
 * User: Igor
 * Date: 01.08.2018
 * Time: 2:19
 *
 * API processing class
 */
class API{
    private $requestMethod = "";
    private $url = false;
    private $headers = array();
    private $body = array();

    /**
     * API constructor.
     * @param string $method
     */
    public function __construct($method = "POST"){
        $this->requestMethod = $method;
    }

    public function setBody($body){
        $this->body = $body;
    }

    /**
     * Set headers for API authorization
     * @param $clientID string
     * @param $token string
     */
    public function authorize($clientID, $token){
        $this->headers[] = 'Accept: application/json';
        $this->headers[] = 'Content-Type: application/json';
        $this->headers[] = 'X-Auth-Client: '.$clientID;
        $this->headers[] = 'X-Auth-Token: '.$token;
    }

    /**
     * Set API URL
     * @param $url string
     */
    public function setURL($url){
        $this->url = $url;
    }

    /**
     * Process request
     */
    public function process(){
        $result = array();
        $apiConnection = curl_init();
        curl_setopt($apiConnection,CURLOPT_URL, $this->url);
        curl_setopt($apiConnection, CURLOPT_CUSTOMREQUEST, $this->requestMethod);
        curl_setopt($apiConnection,CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($apiConnection, CURLOPT_POSTFIELDS, json_encode($this->body));
        curl_setopt($apiConnection, CURLOPT_RETURNTRANSFER, true);
        $apiResult = curl_exec($apiConnection);
        $result['response'] = json_decode($apiResult, true);
        $result['code'] = curl_getinfo($apiConnection,CURLINFO_HTTP_CODE);
        curl_close($apiConnection);
        return $result;
    }
}