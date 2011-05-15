<?php

	$setting["version"]		= "2.05.3";

	############# Configuration #############

	// Set to false to bring the site offline.
	$setting["live"]		= true;

	// Set to true to see PHP errors, warnings and notices.
	$setting["errors"]		= false;

	// Server time zone, for database records. (use: http://www.php.net/manual/en/timezones.php)
	$setting["time_zone"]	= "Europe/London";

	// The default input value.
	$setting["input"]		= "example.com";

	// The max time to check if a site is working for.
	$setting["timeout"]		= 3;

	// Static content override. No trailing slash.
	$setting["static"]		= "http://s3.amazonaws.com/isitup";

	// Folder of script, no trailing slash.
	$setting["folder"]		= "";

	// Set to true to record checks to the database.
	$setting["record"]		= true;
	
	// A list of banned IP addresses, site responds with 403.
	//
	//									 IP Address			   Reason			  	  User Agent
	//									 ==================	   ====================	  ==========
	$setting["banned_ips"]		= array("108.56.179.55",
										"178.162.190.90",
										"178.94.186.10",
										"69.160.84.139",
										"2.100.52.249",
										"82.168.55.205",	//	bot					- Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; Media Center PC 6.0; InfoPath.2; MS-RTC LM 8)
										"192.68.221.135",	// spammer bot			- Mozilla/5.0 (Windows; U; Windows NT 6.1; pt-PT; rv:1.9.2.16) Gecko/20110319 Firefox/3.6.16
										"67.210.218.105",	// spammer bot			- Java/1.6.0_14
										"200.133.215.2",	// automated checker hf - Wget/1.12 (linux-gnu)
										"67.210.218.102", 	// blog.com spammer 	- Java/1.6.0_14
										"80.252.171.68",	// blog.com spammer		- Mozilla/5.0 (compatible; Twingly Recon; twingly.com)
										"178.63.209.74",	// spammer bot 			- Mozilla/5.0 (Windows; U; Windows NT 6.0; en; rv:1.9.0.8) Gecko/2009032609 Firefox/3.0.8 (.NET CLR 3.5.30729)
										"184.73.110.198", 	// blog.com spammer 	- Jakarta Commons-HttpClient/3.1
										"193.228.2.133",);	// iphone scam			- Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.1.3) Gecko/20090824 Firefox/3.5.3

	$setting["banned_ua"]		= array("Mozilla/5.0 (compatible; MJ12bot/v1.3.3; http://www.majestic12.co.uk/bot.php?+)",
										"Python-urllib/1.17 AppEngine-Google; (+http://code.google.com/appengine; appid: runpythoncode)",
										"Java/1.6.0_22",
										"BlogPulse (ISSpider-3.0)",
										"Jakarta Commons-HttpClient/3.0",
										"Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0; FeedFinder-2.0; http://bloggz.se/crawler)",
										"Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1 + FairShare-http://fairshare.cc)",
										"Moreoverbot/5.1 (+http://w.moreover.com; webmaster@moreover.com) Mozilla/5.0",
										"Mozilla/5.0 (compatible; DotBot/1.1; http://www.dotnetdotcom.org/, crawler@dotnetdotcom.org)",
										"Mozilla/5.0 (compatible; Twingly Recon; twingly.com)",
										"gooblog/2.0 (http://help.goo.ne.jp/contact/)",
										"ping.blo.gs/2.0",
										"ping.wordblog.de/ping/1.0",
										"radian6_default_(www.radian6.com/crawler)");

	#########################################

# Don't edit, this processes the settings

// ?admin will always work
if ($_COOKIE["admin"] == true) { $setting["live"] = true; }

// sets the error level
if ($setting["errors"] == true) {
	error_reporting(E_ALL); }
else {
	error_reporting(0); }

// sets the time zone
date_default_timezone_set($setting["time_zone"]);

// sets the timeout
ini_set("default_socket_timeout", $setting["timeout"]);

// check server load and set $setting["record"] accordingly
$load = explode(" ", file_get_contents("/proc/loadavg"));

if ($load[0] > 2.0) { $setting["record"] = false; };

// sets the host domain
$setting["host"] = $_SERVER["SERVER_NAME"];

if ($setting["folder"] != "") {
	$setting["host"] = $setting["host"] . $setting["folder"]; }

if ($setting["static"] == "") {
	$setting["static"] = 'http://' . $setting["host"] . '/static'; }
	
function mint_js() {
	global $setting;
	if ($setting["record"] == true) {
		echo "<script src=\"/mint/?js\" type=\"text/javascript\"></script>\n";
	} else {
		echo "<!-- We are not recording visits at this time. Current load: " . $load[0] . " -->\n";
	}; };