<?php
/**
 * User: Igor
 * Date: 01.08.2018
 * Time: 1:40
 *
 * Categories processing class
 */

class Categories extends Unit {
    /** @var $connection mysqli|bool  */
    private $connection = false;
    private $options = array();
    private $apiConf = array();

    public $errors = array();
    public $originalID = 0;

    public function __construct(){
        global $APIOpt;
        global $dbConnectionConfig;
        global $dbCategoriesConnection; // To prevent MySQLi connections duplicates

        $this->apiConf = $APIOpt;

        if(isset($dbCategoriesConnection) && $dbCategoriesConnection instanceof mysqli && $dbCategoriesConnection->ping()){
            $this->connection = $dbCategoriesConnection;
        }else{
            // Make mysql connection
            $this->connection = new mysqli($dbConnectionConfig['host'], $dbConnectionConfig['user'], $dbConnectionConfig['pass'], $dbConnectionConfig['base']);
            $dbCategoriesConnection = $this->connection;
        }
    }

    /**
     * Set Category option (https://developer.bigcommerce.com/api/v3/#/reference/catalog/categories/create-category)
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
     * Add category via API (returns insertID or false if failed)
     * @param $saveMapping bool
     *
     * @return int|false
     */
    public function save($saveMapping = true){
        if(!$this->connection || !$this->connection->ping()){
            $this->errors[] = "Mysql connection failed";
            return false;
        }
        if(count($this->options) == 0){
            $this->errors[] = "Empty options array";
            return false;
        }
        if(!isset($this->options['name']) || !isset($this->options['parent_id'])){
            $this->errors[] = "Required fields are unspecified";
            return false;
        }

        // Convert parent_id option
        if($this->options['parent_id'] != 0){
            $mappingRecord = $this->getMappingRecord($this->options['parent_id']);
            if(!$mappingRecord || !isset($mappingRecord['big_id'])){
                $this->errors[] = "No mapping record for `parent_id` option with ID: ".$this->options['parent_id'];
                return false;
            }
            $this->options['parent_id'] = $mappingRecord['big_id'];
        }

        // Make API request
        $apiObj = new API();
        $apiObj->setURL($this->apiConf['url'].'catalog/categories');
        $apiObj->authorize($this->apiConf['client'], $this->apiConf['token']);
        $apiObj->setBody($this->options);
        $apiResult = $apiObj->process();
        if($apiResult && isset($apiResult['response']['data']['id'])){
            if($saveMapping){
                $this->saveMappingRecord($this->originalID, $apiResult['response']['data']['id']);
            }
            return $apiResult['response']['data']['id'];
        }else{
            return false;
        }

    }

    /**
     * Save Mapping Record (locally)
     * @param $originalID
     * @param $apiID
     *
     * @return bool
     */
    private function saveMappingRecord($originalID, $apiID){
        if(!$this->connection || !$this->connection->ping()){
            $this->errors[] = "Mysql connection failed";
            return false;
        }
        $this->connection->query("INSERT INTO `cats_ids`(`id`, `big_id`) VALUES( 
          '".$this->connection->real_escape_string($originalID)."', 
          '".$this->connection->real_escape_string($apiID)."'
        )");
        if($this->connection->affected_rows > 0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Get mapping record by ID
     * @param $id int (original id)
     *
     * @return array|false
     */
    private function getMappingRecord($id){
        if(!$this->connection || !$this->connection->ping()){
            $this->errors[] = "Mysql connection failed";
            return false;
        }
        $mappingQuery = $this->connection->query("SELECT * FROM `cats_ids` WHERE `id` = $id");
        if($mappingQuery->num_rows == 0){
            return false;
        }
        $mappingRow = $mappingQuery->fetch_assoc();
        return $mappingRow;
    }
}