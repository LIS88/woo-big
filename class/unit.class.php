<?php
/**
 * User: Igor
 * Date: 01.08.2018
 * Time: 22:11
 */

abstract class Unit{
    abstract function setOption($optName, $optVal);
    abstract function load($id);
}