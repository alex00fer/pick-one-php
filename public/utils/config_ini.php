<?php

define('FILE_PATH', '../config.ini');

function get_config_ini () {
       return parse_ini_file(FILE_PATH);
}