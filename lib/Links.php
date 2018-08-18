<?php
/**
 * This is the Links class.
 *
 * @package com.bbdo.nypdrc
 **/
class Links
{
    private $DB;

    public function __construct(&$theDBConn)
    {
        $this->DB = &$theDBConn;
    }

    private $sql0  = "SELECT tblLinks.linkText as linkText, tblLinks.linkURL as linkURL, tblLinks.description as description, tblLinkCats.CategoryName as CategoryName
		FROM tblLinks INNER JOIN tblLinkCats ON tblLinks.category_id=tblLinkCats.id WHERE tblLinkCats.id = 1 ORDER BY tblLinkCats.id, linkText";

    private $sql1  = "SELECT tblLinks.linkText as linkText, tblLinks.linkURL as linkURL, tblLinks.description as description, tblLinkCats.CategoryName as CategoryName
		FROM tblLinks INNER JOIN tblLinkCats ON tblLinks.category_id=tblLinkCats.id WHERE tblLinkCats.id > 1 ORDER BY tblLinkCats.id, linkText";


    public function getLinkList($sqlID)
    {
        if ($sqlID==1) {
            $sql = $this->sql0;
            $content = "<h1>Links &amp; Resources</h1>\n";
        } else {
            $sql = $this->sql1;
            $content = "";
        }
        $rs = $this->DB->Execute($sql);
        $theHeader = "";
        foreach ($rs as $row) {
            if ($row['CategoryName']<>$theHeader) {
                if ($theHeader<>"") {
                    $content .= "</ul>\n";
                }
                $content .= "<br><h2>" . $row['CategoryName'] . "</h2>\n<ul>";
                $theHeader = $row['CategoryName'];
            }
            $content .= "<li><a href=\"" . $row['linkURL'] . "\" target=\"_blank\">" . $row['linkText'] . "</a>";
            if (trim($row['description'])<>"") {
                $content .= " - " . $row['description'];
            }
            $content .= "</li>\n";
        }
        return $content;
    }
}
