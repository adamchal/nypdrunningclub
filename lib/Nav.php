<?php
/**
 * This is the Nav class.
 *
 * @package com.bbdo.nypdrc
 **/
class Nav
{
    private $pageID;
    private $pageName;
    private $site;

    private $pages = array(
        0 => array(0,'Home','/'),
        1 => array(1,'About Us (Membership, Directors and Contact Info)','about'),
        2 => array(2,'Upcoming Events','upcoming'),
        3 => array(3,'Past Events','past'),
        4 => array(4,'Links and Resources', 'links'),
        5 => array(5,'Forum', 'forum')
    );

    /**
     * Constructor for the Navigation class.
     *
     * @param int $pageNum page identifier
     * @param array $theSite site configuration array
     * @param string $pageName
     */
    public function __construct($pageNum, $theSite, $pageName="")
    {
        $this->pageID = intval($pageNum);
        $this->site = $theSite;
        $this->pageName = $pageName;
    }

    public function drawBox()
    {
        $val  = "<div id=\"topnav\">\n";

        $val .= "\t\t\t<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"987\">\n";
        $val .= "\t\t\t\t<tr>\n";
        $val .= "\t\t\t\t\t<td width=\"184\"><a href=\"". $this->site['baseURL'] . "\">";
        $val .= "<img src=\"". $this->site['baseURL'] . "img/hdr1Left.gif\" height=\"88\" width=\"184\" alt=\"NYPD Running Club\"></a></td>\n";
        $val .= "\t\t\t\t\t<td>&nbsp;&nbsp;";
        if ($this->pageID > 0) {
            $val .= $this->getBreadcrumb();
        }
        $val .= "</td>\n";
        $val .= "\t\t\t\t</tr>\n";
        $val .= "\t\t\t</table>\n";

        $val .= "\t\t</div>\n";
        $val .= "\t\t<div id=\"nav\">\n";
        $val .= "\t\t\t<div id=\"navItems\">\n";
        $val .= "\t\t\t\t<img src=\"" . $this->site['baseURL'] . "img/nav/top.gif\" alt=\"\">\n";
        for ($i=1;$i<6;$i++) {
            $val .= $this->navDrawItem($this->pages[$i]);
        }
        $val .= "\t\t\t\t<img src=\"" . $this->site['baseURL'] . "img/nav/btm.gif\" alt=\"\">\n";
        $val .= "\t\t\t</div>\n";
        $val .= "\t\t</div>\n";
        return $val;
    }

    public function getBodyPreloader()
    {
        $folder = $this->site['baseURL'] . "img/nav/";
        $val  = "onLoad=\"BBDO_preloadImages(";
        for ($i=1;$i<6;$i++) {
            $val .= "'" . $folder . "h/" . $i . ".gif'";
            if ($i<5) {
                $val .= ",";
            }
        }
        $val .= ");\"";
        return $val;
    }

    public function getSiteName()
    {
        if ($this->pageName<>"") {
            $val = $this->site['siteName'] . " - " . $this->pages[$this->pageID][1] . ' - ' . $this->pageName;
        } elseif ($this->pageID > 0) {
            $val = $this->site['siteName'] . " - " . $this->pages[$this->pageID][1];
        } else {
            $val = $this->site['siteName'];
        }
        return $val;
    }

    public function drawFooter()
    {
        $val  = "<div id=\"footer\">\n\t\t\t";
        $val .= "Copyright &copy;2008-".date('Y').", <strong>NYPD Running Club</strong>. All rights reserved.<br>\n";
        return $val;
    }

    private function getBreadcrumb()
    {
        $val = "<a href=\"" . $this->site['baseURL'] . "\">Home</a> &raquo; ";
        if ($this->pageName<>"") {
            // We're on a tertiary page
            $val .= "<a href=\"" . $this->site['baseURL'] . $this->pages[$this->pageID][2] . "\">" . $this->pages[$this->pageID][1] . "</a> &raquo; ";
            $val .= "<strong>" . $this->pageName;
        } else {
            // We're on a secondary page
            $val .= "<strong>" . $this->pages[$this->pageID][1];
        }
        $val .= "</strong>";
        return $val;
    }

    private function navDrawItem($item)
    {
        $code = "\t\t\t\t";
        if (intval($item[0])==$this->pageID) {
            // This is the page I'm on - draw the "S" version
            $code .= "<img src=\"" . $this->site['baseURL'] . "img/nav/s/" . $item[0] . ".gif\" height=\"31\" width=\"184\" alt=\"" . $item[1] . "\">";
        } else {
            // This is not the page I'm on - draw the "N" version with the "H" hover.
            $code  .= "<a href=\"" . $this->site['baseURL'] . $item[2] . "\">";
            $code .= "<img src=\"" . $this->site['baseURL'] . "img/nav/n/" . $item[0] . ".gif\" id=\"nav".$item[0]."\"";
            $code .= " height=\"31\" width=\"184\" alt=\"" . $item[1] . "\"";
            $code .= " onMouseOver=\"BBDO_swapImage('nav".$item[0]."','','".$this->site['baseURL']."img/nav/h/".$item[0].".gif',1)\" onMouseOut=\"BBDO_swapImgRestore()\">";
            $code .= "</a>";
        }
        $code .= "\n";
        return $code;
    }
}
