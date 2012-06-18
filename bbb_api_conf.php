<?php
/**
 * Elgg Webinar
 *
 * @package Elgg.Webinar
 */

// given by command line : bbb-conf --salt

// This is the URL for the BigBlueButton server 
//Make sure the url ends with /bigbluebutton/
$this->server_url = "http://example.com/bigbluebutton/";
// This is the security salt that must match the value set in the BigBlueButton server
$this->server_salt = "1234567890abcdefghijklmnopqrstuvwxyz";

$this->admin_pwd = 'admin';
$this->user_pwd = 'user';
