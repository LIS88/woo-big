<?php
/**
 * User: Igor
 * Date: 01.08.2018
 * Time: 21:34
 *
 * String processing class
 */

class StringProcessor{
    static function normalizeSpecialCharts($str){
        $str = str_replace("&amp;", "&");
        return $str;
    }
}