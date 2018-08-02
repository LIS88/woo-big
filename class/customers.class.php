<?php
/**
 * User: Igor
 * Date: 02.08.2018
 * Time: 13:13
 *
 * Customer processing class
 */

class Customers extends Unit{
    /** @var $connection mysqli|bool  */
    private $connection = false;
    private $options = array();
    private $apiConf = array();
    private $updateMode = "";

    public $errors = array();
    public $originalID = 0;

    /**
     * Customers constructor.
     * @param $updateMode string (updateExisting|skipExisting)
     */
    public function __construct($updateMode = 'updateExisting'){
        global $APIOpt;
        global $dbConnectionConfig;
        global $dbCustomersConnection; // To prevent MySQLi connections duplicates

        $this->apiConf = $APIOpt;
        $this->updateMode = $updateMode;

        if(isset($dbCustomersConnection) && $dbCustomersConnection instanceof mysqli && $dbCustomersConnection->ping()){
            $this->connection = $dbCustomersConnection;
        }else{
            // Make mysql connection
            $this->connection = new mysqli($dbConnectionConfig['host'], $dbConnectionConfig['user'], $dbConnectionConfig['pass'], $dbConnectionConfig['base']);
            $dbCustomersConnection = $this->connection;
        }
    }

    /**
     * Set Customer option (https://developer.bigcommerce.com/api/v2/#customer-object-properties)
     * @param $optName string
     * @param $optVal string
     *
     * @return bool
     */
    public function setOption($optName, $optVal){
        if(!$this->connection || !$this->connection->ping()){
            $this->errors[] = "Mysql connection failed";
            return false;
        }
        if(strlen($optName) < 2){
            $this->errors[] = "Incorrect option name - ".$optName;
            return false;
        }
        $this->options[$optName] = $optVal;
        return true;
    }

    /**
     * Load customer from API by ID
     * @param $id int
     *
     * @return array|false
     */
    public function load($id){
        // Make API request
        $apiObj = new API("GET");
        $apiObj->setURL($this->apiConf['url'].'customers/'.$id);
        $apiObj->authorize($this->apiConf['client'], $this->apiConf['token']);
        $apiResult = $apiObj->process();
        if($apiResult && isset($apiResult['response']['data']['id'])){
            return $apiResult['response']['data'];
        }else{
            return false;
        }
    }
}