<?php

$site = array( // Production
    'siteName' => "NYPD Running Club",
    'baseURL'  => "/"
);

$dbConfig = array(
    'server'	=> '127.0.0.1',
    'user'		=> 'root',
    'password'	=> 'rd112358',
    'database'	=> 'nypdrunningclub'
);

// Includes all classes required
include "adodb5/adodb.inc.php";
include "Nav.php";

$DB = NewADOConnection('mysql');
$DB->Connect($dbConfig['server'], $dbConfig['user'], $dbConfig['password'], $dbConfig['database']);


/**
 * General housekeeping functions
 */

function die301Death()
{
    // header ('HTTP/1.1 301 Moved Permanently');
    // header ('Location: /');
    // die;
    print "SPANK.";
}
