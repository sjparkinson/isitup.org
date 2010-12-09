<?php

	$setting["version"]		= "2.03.5";

############# Configuration #############

	// Set to false to bring the site offline.
	$setting["live"]		= true;

	// Set to true to see PHP errors, warnings and notices.
	$setting["errors"]		= false;

	// Server time zone, for database records. (use: http://www.php.net/manual/en/timezones.php)
	//$setting["time_zone"]	= "Europe/London";

	// The default input value.
	$setting["input"]		= "example.com";

	// The domains to be use by autocomplete.
	$setting["auto_domains"] = array(
		"4chan.org",
		"12chan.org",
		"anontalk.com",
		"bbc.co.uk",
		"ebay.com",
		"example.com",
		"facebook.com",
		"flickr.com",
		"fmylife.com",
		"gmail.com",
		"google.com",
		"imgur.com",
		"isitup.org",
		"mylifeisaverage.com",
		"paypal.com",
		"reddit.com",
		"slashdot.org",
		"thepiratebay.org",
		"twitter.com",
		"wikileaks.org",
		"wikipedia.com",
		"youtube.com");

	// The max time to check if a site is working for.
	$setting["timeout"]		= 3;

	// Static content override. No trailing slash.
	//$setting["static"]		= "http://static.im/isitup";

	// Folder of script, no trailing slash.
	$setting["folder"]		= "";

#########################################

# Don't edit, this processes the settings

// ?admin will always work
<<<<<<< HEAD
if (isset($_COOKIE["admin"])) { $setting["live"] = true; }
=======
if ( isset($_COOKIE["admin"]) ) { $setting["live"] = true; }
>>>>>>> ab467170c6d144314985b1c3c4fbf1ce7a55bf8c

// sets the error level
if ($setting["errors"] == true) {
	error_reporting(E_ALL); }
else {
	error_reporting(0); }

// sets the time zone
date_default_timezone_set($setting["time_zone"]);

// sets the timeout
ini_set("default_socket_timeout", $setting["timeout"]);

// sets the host domain
$setting["host"] = $_SERVER["SERVER_NAME"];

if ($setting["folder"] != "") {
	$setting["host"] = $setting["host"] . $setting["folder"]; }

if ($setting["static"] == "") {
	$setting["static"] = 'http://' . $setting["host"] . '/static'; }