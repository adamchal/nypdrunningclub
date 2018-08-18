<?php
/**
 * This is the Event class. It defines a running event, and provides for
 * display of all summary and detail information for a single event or
 * a list of events.
 *
 * @package com.bbdo.nypdrc
 **/
class Event
{
    private $DB;
    private $site;
    private $sql = array(
        'upcomingAll' => "SELECT id, eventTitle, eventDateTime, location, left(description, 150) as 'description'
				FROM tblEvents
				WHERE eventDateTime > now()
				ORDER BY eventDateTime;",
        'upcoming5' => "SELECT id, eventTitle, eventDateTime, location, left(description, 150) as 'description'
				FROM tblEvents
				WHERE eventDateTime > now()
				ORDER BY eventDateTime
				LIMIT 5;",
        'pastAll' => "SELECT id, eventTitle, eventDateTime, location, left(description, 150) as 'description', date_format(eventDateTime, '%Y') as 'year'
				FROM tblEvents
				WHERE eventDateTime < now()
				ORDER BY eventDateTime DESC;",
        'justOne' => "SELECT id, eventTitle, eventDateTime, description, location, directions, priceInfo, contactInfo,
					photoRSSURL, resultsFileURL, moreInfoURL, registerURL, recordCreated, recordModified
				FROM tblEvents
				WHERE id=",
        'latestResults' => "SELECT id, eventTitle, resultsFileURL, eventDateTime
				FROM tblEvents
				WHERE resultsFileURL<>'' AND eventDateTime < now()
				ORDER BY eventDateTime DESC
				LIMIT 5"
    );
    private $detail = array(
        'id'				=> 0,
        'eventTitle'		=> '',
        'eventDateTime' 	=> '',
        'description'		=> '',
        'location'			=> '',
        'directions'		=> '',
        'priceInfo'			=> '',
        'contactInfo'		=> '',
        'photoRSSURL'		=> '',
        'resultsFileURL'	=> '',
        'moreInfoURL'		=> '',
        'registerURL'		=> '',
        'whenType'			=> 0 // will set to PAST_EVENT or UPCOMING_EVENT
    );

    public function __construct(&$dbConn, $siteData, $theID)
    {
        $this->DB = &$dbConn;
        $this->site = $siteData;
        $this->detail['id'] = intval($theID);
        if ($this->detail['id'] > 0) {
            $rs = $this->DB->Execute($this->sql['justOne'] . $this->detail['id']);
            // var_dump($this->sql['justOne'] . $this->detail['id']);exit;
            if ($rs) {
                // RECORD FOUND
                $this->detail = $rs->fields;
                $this->detail['photoRSSURL'] = intval(trim($this->detail['photoRSSURL']));
                if (date("Ymd", strtotime($rs->fields['eventDateTime'])) >= date("Ymd")) {
                    $this->detail['whenType'] = 2;
                } else {
                    $this->detail['whenType'] = 3;
                }
            } else {
                // RECORD NOT FOUND
                $this->detail['eventTitle'] = "Event not found.";
                // Probably won't run...
            }
        }
    }

    public function getEventType()
    {
        return $this->detail['whenType'];
    }

    /**
     * Returns a list of the next 5 upcoming events
     *
     * @return string HTML
     */
    public function getUpcoming5()
    {
        $content  = "<h2><strong>Featured Upcoming Events</strong></h2>\n";
        $rs = $this->DB->Execute($this->sql['upcoming5']);
        foreach ($rs as $row) {
            $content .= "<p><a href=\"";
            $content .= $this->site['baseURL'] . "event-" . $row['id'];
            $content .= "\"><strong>" . $row['eventTitle'] . "</strong></a><br>\n";
            $content .= $this->getPrettyDate($row['eventDateTime'], 1). "<br>\n";
            $content .= $row['location'];
            //			if (trim ($row['description'])<>"" ) {
//				$content .= " - ";
//				if (strlen(trim($row['description']))<150 ) {
//					$content .= $row['description'];
//				} else {
//					$content .= substr($row['description'],0,150) . "...";
//				}
//			}
        }
        return $content;
    }

    /**
     * Returns up to the 5 latest race results
     */
    public function getLatestResults()
    {
        $rs = $this->DB->Execute($this->sql['latestResults']);
        if ($rs) {
            $content  = "<h2><strong>Latest Race Results</strong></h2>\n";
            foreach ($rs as $row) {
                $content .= "<p><strong>" . $row['eventTitle'] . "</strong>  (" . $this->getPrettyDate($row['eventDateTime'], 5). ")<br>(<a href=\"";
                $content .= $this->site['baseURL'] . "event-" . $row['id'];
                $content .= "\">Event Details</a>) (";
                $content .= "<a href=\"" . $row['resultsFileURL'] . "\" target=\"_blank\">Results</a>)</p>\n";
            }
        } else {
            // NO RESULTS
            $content = "";
        }
        return $content;
    }

    /**
     * Returns all upcoming events
     *
     * @return string HTML
     */
    public function getAllUpcoming()
    {
        $content = "<h1>Upcoming Events</h1>\n";
        $content .= "<table width=\"100%\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\">\n";

        $rs = $this->DB->Execute($this->sql['upcomingAll']);
        if ($rs) {
            foreach ($rs as $row) {
                $content .= "\t<tr valign=\"top\">\n";

                $content .= "\t\t<td width=\"150\">" . $this->getPrettyDate($row['eventDateTime'], 4) . "<br>\n\t\t\t";
                $content .= $this->getPrettyDate($row['eventDateTime'], 3) . "</td>\n";

                $content .= "\t\t<td><strong><a href=\"";
                $content .= $this->site['baseURL'] . "event-" . $row['id'];
                $content .= "\">" . $row['eventTitle'];
                $content .= "</a></strong> ";

                $content .= " (" . $row['location'] . ")";


                if (trim($row['description'])<>"") {
                    $content .= "<br>" . $row['description'] . "...";
                }
                $content .= "</td>\n";
                $content .= "\t</tr>\n";
            }
        } else {
            $content .= "<tr><td>There are no past events presently available.</td></tr>";
        }
        $content .= "</table>";
        return $content;
    }

    /**
     * Returns list of all past events
     *
     * @return string HTML
     */
    public function getAllPast()
    {
        $content = "<h1>Past Events</h1>\n";
        $content .= "<table width=\"100%\" border=\"0\" cellpadding=\"5\" cellspacing=\"0\">\n";

        $rs = $this->DB->Execute($this->sql['pastAll']);
        if ($rs) {
            $thisYear = "";
            foreach ($rs as $row) {
                $content .= "\t<tr valign=\"top\">\n";

                if ($thisYear<>$row['year']) {
                    $thisYear = $row['year'];
                    $content .= "\t\t<td>" . $row['year'] . "</td>\n";
                } else {
                    $content .= "\t\t<td>&nbsp;</td>\n";
                    //					$content .= "\t\t<td>" . $this->getPrettyDate($row['eventDateTime'], 2 ) . "</td>\n";
                }
                $content .= "\t\t<td>";
                $content .= $this->getPrettyDate($row['eventDateTime'], 2);
                $content .= " - <strong><a href=\"";
                $content .= $this->site['baseURL'] . "event-" . $row['id'];
                $content .= "\">" . $row['eventTitle'];
                $content .= "</a></strong> \n";
                if (trim($row['location'])<>"") {
                    $content .= " (" . trim($row['location']) . ") ";
                }

                //				if (trim ($row['description'])<>"" ) {
                //					$content .= "<br>" . $row['description'] . "...";
                //				}
                $content .= "</td>\n";
                $content .= "\t</tr>\n";
            }
        } else {
            $content .= "<tr><td>There are no past events presently available.</td></tr>";
        }
        $content .= "</table>";
        return $content;
    }

    /**
     * Returns the event name.
     *
     * @return string the Event Title
     */
    public function getEventTitle()
    {
        return trim($this->detail['eventTitle']);
    }

    public function getOneLeftContent()
    {
        $val  = "<h1>" . $this->detail['eventTitle'] . "</h1>\n";
        $val .= "<p><strong>" . $this->getPrettyDate($this->detail['eventDateTime']) . "</strong></p>\n";
        $val .= $this->detail['description'];
        if ($this->detail['whenType'] == 2) {
            // Upcoming Event
            $val .= "<p><a href=\"upcoming\">See More Upcoming Events &raquo;</a>";
        } else {
            // Past Event
            $val .= "<p><a href=\"past\">See More Past Events &raquo;</a>";
        }
        return $val;
    }

    public function getOneRightContent()
    {
        // Shouldn't get here unles eventType is valid, so:
        $val  = "<div id=\"sidebar\">\n";
        if ($this->detail['whenType'] == 2) {
            // event is 2 = Upcoming Event
            $val .= $this->getCleanSidebarData("Where", $this->detail['location']);
            $val .= $this->getCleanSidebarData("Cost", $this->detail['priceInfo']);
            $val .= $this->getCleanSidebarData("Contact Info", $this->detail['contactInfo']);
            $val .= $this->getCleanSidebarData("Directions", $this->detail['directions']);
            $val .= $this->getCleanSidebarData("For More Information", $this->detail['moreInfoURL'], true);
            if (trim($this->detail['registerURL'])<>'') {
                $val .= "</div>\n";
                $val .= "<div id=\"subSidebar\" style=\"text-align:center;\">\n";
                $val .= "<a href=\"" . trim($this->detail['registerURL']) . "\" target=\"_blank\"><img src=\"img/btnEvent.gif\"></a>";
            }
        } else {
            // event is 3 = Past Event
            $val .= $this->getCleanSidebarData("Results", $this->detail['resultsFileURL'], true);
            $val .= $this->getCleanSidebarData("For More Information", $this->detail['moreInfoURL'], true);
            $val .= "</div>\n<div id=\"subSidebar\">\n";

            // If there is a gallery attached, attempt to draw it.
            if ($this->detail['photoRSSURL'] > 0) {
                include "lib/PhotoFeed.php";
                $photos = new PhotoFeed($this->detail['photoRSSURL']);
                $val .= $photos->getGallery();
            }
        }
        $val .= "</div><br><br>\n";
        return $val;
    }

    private function getPrettyDate($data, $format=1)
    {
        switch ($format) {
            case 1: $val = date("l, F jS, Y g:i A", strtotime($data));
                    break;
            case 2: $val = date("F j", strtotime($data));
                    break;
            case 3: $val = date("g:i A", strtotime($data));
                    break;
            case 4: $val = date("M j, Y", strtotime($data));
                    break;
            case 5: $val = date("n/j/Y", strtotime($data));
                    break;
            default: $val = date("l, F jS, Y g:i A", strtotime($data));
                     break;
        }
        return $val;
    }

    private function getCleanSidebarData($label, $data, $asLink=false)
    {
        if (trim($data)<>"") {
            // Data is here, so send it.
            if ($asLink) {
                $val = "<h2>" . $label . "</h2>\n<a href=\"" . $data . "\" target=\"_blank\">" . $label . " (External Link)</a><br>\n";
            } else {
                $val = "<h2>" . $label . "</h2>\n" . $data . "<br>\n";
            }
        } else {
            // No data, so return nothin'
            $val = "";
        }
        return $val;
    }

    public function debugData()
    {
        print "<pre>";
        print_r($this->detail);
        print "</pre>";
    }
}
