<?php
require_once("lib/config.php");

$pageID = 0;
$eventID = 0;
$pageName = "";

switch (preg_replace('/\/([^\/]+).*?$/', '$1', $_SERVER['REQUEST_URI'])) {
    case 'about':
        $_GET['pageID'] = 1;
        break;
    case 'upcoming':
        $_GET['pageID'] = 2;
        break;
    case 'past':
        $_GET['pageID'] = 3;
        break;
    case 'links':
        $_GET['pageID'] = 4;
        break;
    case 'forum':
        $_GET['pageID'] = 5;
        break;
    default:
        if (preg_match('/event-[0-9]/', $_SERVER['REQUEST_URI'])) {
            $_GET['eventID'] = preg_replace('/^.*?event-([0-9]+).*?$/', '$1', $_SERVER['REQUEST_URI']);
            $_GET['pageID'] = 6;
        } else {
            $_GET['pageID'] = 0;
        }
        break;
}

if (isset($_GET['pageID'])) {
    if ($_GET['pageID'] > 0 && $_GET['pageID'] < 7) {
        $pageID = intval($_GET['pageID']);
        if (($pageID == 6 || $pageID == 2 || $pageID==3)  && isset($_GET['eventID'])) {
            $eventID = intval($_GET['eventID']);
            if ($eventID < 1) {
                $eventID = 0;
            }
        } else {
            $eventID = 0;
        }
    } else {
        $pageID = 0;
    }
}

$leftContent = "";
$rightContent = "";

switch ($pageID) {
    case 0: // Home
            include "lib/Event.php";
            $nav = new Nav(0, $site, "");
            $events = new Event($DB, $site, 0);
            $leftContent = file_get_contents("content/home-left.txt");
            $rightContent = file_get_contents("content/home-right.txt") . $events->getUpcoming5() . $events->getLatestResults();
            include "template/2col.php";
            break;

    case 1: // Membership
            $leftContent = file_get_contents("content/membership-left.txt");
            $rightContent = file_get_contents("content/membership-right.txt");
            $nav = new Nav(1, $site, "");
            include "template/2col.php";
            break;

    case 2: // Upcoming Events
            include "lib/Event.php";
            $events = new Event($DB, $site, 0);
            $content = $events->getAllUpcoming();
            $nav = new Nav(2, $site, "");
            include "template/1col.php";
            break;

    case 3: // Past Events
            include "lib/Event.php";
            $events = new Event($DB, $site, 0);
            $content = $events->getAllPast();
            $nav = new Nav(3, $site, "");
            include "template/1col.php";
            break;

    case 4: // Links and Resources
            include "lib/Links.php";
            $content = new Links($DB);
            $leftContent = $content->getLinkList(1);
            $rightContent = $content->getLinkList(2);
            $nav = new Nav(4, $site, "");
            include "template/2col.php";
            break;

    case 5: // Discussion Forum
            $nav = new Nav(5, $site, "");
            $leftContent = file_get_contents("content/forum-left.txt");
            $rightContent = file_get_contents("content/forum-right.txt");
            include "template/2col.php";
            break;

    case 6: // Event Detail
            include "lib/Event.php";
            $event = new Event($DB, $site, $eventID);
            if ($event->getEventTitle()<>"") {
                // This is a valid event
                $nav = new Nav($event->getEventType(), $site, $event->getEventTitle());
                $leftContent = $event->getOneLeftContent();
                $rightContent = $event->getOneRightContent();
            } else {
                print " ERROR.";
                //				die301Death();
            }
            include "template/2col.php";
            break;
}
