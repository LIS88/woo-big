<?php
/**
 * User: Igor
 * Date: 01.08.2018
 * Time: 3:23
 *
 * Process categories
 */

require_once '/root/woo-big/init.php';

$connection = new mysqli($dbConnectionConfig['host'], $dbConnectionConfig['user'], $dbConnectionConfig['pass'], $dbConnectionConfig['base']);

if(!$connection || !$connection->ping()){
    die("DB ERROR");
}

$queryCategories = $connection->query("SELECT * FROM `cats` ORDER BY `id` ASC");
if($queryCategories->num_rows == 0){
    die("Nothing to import");
}
while($curCategory = $queryCategories->fetch_assoc()){
    $categoryObj = new Categories();
    $categoryObj->originalID = $curCategory['id'];
    $categoryObj->setOption('name', $curCategory['cat_name']);
    $categoryObj->setOption('parent_id', $curCategory['parent_id']);
    $saveResult = $categoryObj->save();
    if($saveResult){
        echo "[SUCCESS] Category '".$curCategory['cat_name']."' was added with ID: ".$saveResult.PHP_EOL;
    }else{
        echo "[ERROR] Failed to add Category '".$curCategory['cat_name']."'".PHP_EOL;
        if(count($categoryObj->errors) > 0){
            foreach($categoryObj->errors AS $curError){
                echo " - ERROR: ".$curError.PHP_EOL;
            }
        }
    }
}