<?php
    include('adodb5/adodb.inc.php');
    $db = ADONewConnection($database);
    $db->debug = true;
    $db->Connect($server, $user, $password, $database);
    $rs = $db->Execute('select * from some_small_table');
    print "<pre>";
    print_r($rs->GetRows());
    print "</pre>";
