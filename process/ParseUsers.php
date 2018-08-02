<?php
/**
 * User: Igor
 * Date: 02.08.2018
 * Time: 17:20
 *
 * Users parsing script
 */

require_once '/root/woo-big/init.php';

$originalDBConnection = new mysqli($dbConnectionConfig['host'], $dbConnectionConfig['user'], $dbConnectionConfig['pass'], "pishop");
$connection = new mysqli($dbConnectionConfig['host'], $dbConnectionConfig['user'], $dbConnectionConfig['pass'], $dbConnectionConfig['base']);

if(!$originalDBConnection || !$originalDBConnection->ping()){
    die("DB ERROR #1");
}

if(!$connection || !$connection->ping()){
    die("DB ERROR #2");
}

// Get users
$usersQuery = $originalDBConnection->query("SELECT * FROM `wp_users` ORDER BY `ID` ASC");
if($usersQuery->num_rows == 0){
    die("No Users to export");
}
while($userRow = $usersQuery->fetch_assoc()){
    $toInsertArray = array();
    $additionalDataArray = array();
    $curProcessingUserID = $userRow['id'];
    $toInsertArray["`id`"] = $curProcessingUserID;
    $toInsertArray["`login`"] = $connection->real_escape_string($userRow['user_login']);
    $toInsertArray["`email`"] = $connection->real_escape_string($userRow['user_email']);

    $curUserMetaArray = array();
    $userMetaQuery = $originalDBConnection->query("SELECT * FROM `wp_usermeta` WHERE `user_id` = $curProcessingUserID");
    if($userMetaQuery->num_rows == 0){
        echo "User '".$toInsertArray["`login`"]."' skipped. No meta data found.".PHP_EOL;
        continue;
    }
    while($userMetaRow = $userMetaQuery->fetch_assoc()){
        $curUserMetaArray[$userMetaRow['meta_key']] = $userMetaRow['meta_value'];
    }
}


// --- FUNCTIONS -------------------------------------------------------------------------------------------------------
function getFirstLastName($metaArr){
    $result = array();
    if(isset($metaArr['first_name']) && strlen($metaArr['first_name'])>1){
        $result['first_name'] = $metaArr['first_name'];
    }elseif(isset($metaArr['billing_first_name']) && strlen($metaArr['billing_first_name'])>1){
        $result['first_name'] = $metaArr['billing_first_name'];
    }elseif(isset($metaArr['shipping_first_name']) && strlen($metaArr['shipping_first_name'])>1){
        $result['first_name'] = $metaArr['shipping_first_name'];
    }else{
        return false;
    }

    if(isset($metaArr['last_name']) && strlen($metaArr['last_name'])>1){
        $result['last_name'] = $metaArr['last_name'];
    }elseif(isset($metaArr['billing_last_name']) && strlen($metaArr['billing_last_name'])>1){
        $result['last_name'] = $metaArr['billing_last_name'];
    }elseif(isset($metaArr['shipping_last_name']) && strlen($metaArr['shipping_last_name'])>1){
        $result['last_name'] = $metaArr['shipping_last_name'];
    }else{
        return false;
    }
    return $result;
}