<?php
/**
 * This is the PhotoFeed class. It extracts the RSS feed from SmugMug.com and displays it on the local site.
 *
 * @package com.bbdo.nypdrc
 **/
class PhotoFeed
{

    /**
     * Sets up namespace property -- basically, how to interpret the incoming XML.
     *
     * @var Array
     */
    private $namespace = array(
        "geo" 	=> "http://www.w3.org/2003/01/geo/wgs84_pos#",
        "exif"	=> "http://www.exif.org/specifications.html",
        "media"	=> "http://search.yahoo.com/mrss/"
    );

    /**
     * Sets up common configuration information, such as the SmugMug base URL
     *
     * @var Array
     */
    private $config = array(
        "base"		=> "http://nypdrc.smugmug.com/hack/feed.mg?Type=gallery&format=rss200&Data=",
        "galleryID"	=> 0
    );

    /**
     * Convenience variable, set on __construct. Combines information from the $config array.
     *
     * @var String
     */
    private $url = "";

    /**
     * Holds all the individual elements of the RSS feed
     *
     * @var Array
     */
    private $gallery = array();

    /**
     * Holds the raw XML
     *
     * @var SimpleXmlElement
     */
    private $xml;

    /**
     * Channel metadata
     *
     * @var Array
     */
    private $channel = array(
        'title'			=> '',
        'link'			=> '',
        'description'	=> '',
        'pubDate'		=> '',
        'generator'		=> '',
        'copyright'		=> ''
    );

    /**************************************************************************************************/

    public function __construct($galleryID)
    {

        // STEP 01 - SET UP THE OBJECT
        $this->config['galleryID'] = intval($galleryID);
        $this->url = $this->config['base'] . $this->config['galleryID'];

        // STEP 02 - GET THE FEED
        $rawFeed = file_get_contents($this->url);
        $this->xml = new SimpleXmlElement($rawFeed);

        // STEP 03 - EXTRACT CHANNEL METADATA
        $this->channel['title'] = $this->xml->channel->title;
        $this->channel['link'] = $this->xml->channel->link;
        $this->channel['description'] = $this->xml->channel->description;
        $this->channel['pubDate'] = $this->xml->channel->pubDate;
        $this->channel['generator'] = $this->xml->channel->generator;
        $this->channel['copyright'] = $this->xml->channel->copyright;

        // STEP 04 - EXTRACT PHOTO ITEMS
        $numItems = 0;
        foreach ($this->xml->channel->item as $item) {
            $photo = array();
            $photo['title'] = $item->title;
            $photo['link'] = $item->link;
            $photo['guid'] = $item->guid;
            array_push($this->gallery, $photo);
            $numItems++;
        }
    }

    public function getGallery()
    {
        $val = "";
        $val .= "There are " . count($this->gallery) . " photos from this event.";
        if (count($this->gallery) > 9) {
            $val .= " Here are just a few of them -- view the <a href=\"";
            $val .= "http://nypdrc.smugmug.com/gallery/" . $this->config['galleryID'];
            $val .= "\" target=\"photos\">entire gallery here</a>.<br>";
        }
        $val .="<div style=\"\">";

        foreach (array_slice($this->gallery, 0, 9) as $photo) {
            $val .= "<a href=\"" . trim($photo['link']) . "\" target=\"photos\"><img src=\"". trim($photo['guid']) ." \"></a>\n";
        }
        $val .="</div>\n";
        return $val;
    }
}
