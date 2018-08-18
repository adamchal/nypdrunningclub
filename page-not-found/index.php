<?php

require_once("../lib/config.php");

$content = file_get_contents("../content/page-not-found.txt");
$nav = new Nav(0, $site, "");
include "../template/1col.php";
