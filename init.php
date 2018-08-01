<?php
/**
 * User: Igor
 * Date: 01.08.2018
 * Time: 1:39
 */

// API connection OPTIONS
$APIOpt = array();
$APIOpt['client'] = "ao5jw89aqit45mr0tlrsixewazoqnv5";
$APIOpt['token'] = "fo1t3r9wlnj1vvgxatnmeji360zd5p6";
$APIOpt['url'] = "https://api.bigcommerce.com/stores/ljk5u2aeef/v3/";

// DB Connection config
$dbConnectionConfig = array();
$dbConnectionConfig['host'] = "localhost";
$dbConnectionConfig['user'] = "root";
$dbConnectionConfig['pass'] = "pass4root";
$dbConnectionConfig['base'] = "pishop_migrate";

require_once '/root/woo-big/class/helpers/StringProcessor.class.php';

require_once '/root/woo-big/class/API.class.php';
require_once '/root/woo-big/class/unit.class.php';
require_once '/root/woo-big/class/categories.class.php';

