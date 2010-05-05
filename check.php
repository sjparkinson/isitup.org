<?php
// if the site is offline
require_once("settings.php");
if ($setting["live"] != true) {
	header('Location: http://' . $setting["host"] . '/offline');
	exit(); }

require_once("functions.php");
//require_once("classes.php");

// retrieve the url to test, then clean it up
$domain = preg_replace("/[^A-Za-z0-9-\/\.\:]/", "", trim($_GET["domain"]));

// if domain has a ":", break up the url into domain & port, setting them to seperate variables
if (strpos($domain, ":") != false) {

	// split the variable into two, $domain & $port
	list($domain, $port) = explode(":", $domain);

	// if the port is not numeric or not set we use port 80
	if (!is_numeric($port) || empty($port)) { $port = 80; }; }
else {
	$port = 80; };

// check the site and get the messages

// start the timmer
$time_start = microtiming();

$code = get_response($domain, $port);

// stop the timmer
$time_stop = microtiming();

// caluate and format the time taken to connect
$time	= round($time_stop - $time_start, 3);

$id		= gen_id($code);
$title	= gen_title($id, $domain);
$html	= gen_html($id, $domain, $port, $time, $code);

if ($id == 1 || $id == 2) {

	set_auto_domains($domain, $port, $setting["auto_domains"]);

//	$db = new Database(array (
//		'host'	=> 'localhost',
//		'user'	=> '',
//		'pass'	=> ''));

//	if ($db->connect()) {
//		if ($db->select_db("isitup")) {
//			if (empty($code)) { $code = "null"; };
//			if (preg_match("/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/i", gethostbyname($domain))) {
//				$response_ip = "\"".gethostbyname($domain)."\"";
//			} else {
//				$response_ip = "null";
//			};
//			$data = array (
//				"time"				=> time(),
//				"remote_address"	=> str_replace("::ffff:", "", $_SERVER['REMOTE_ADDR']),
//				"domain"			=> $domain,
//				"response_ip"		=> $response_ip,
//				"response_code"		=> $code,
//				"response_status"	=> $id,
//				"response_time"		=> $time);

//			$safe_data = $db->clean($data);

//			$sql = "INSERT INTO `check` VALUES (null, \"".$safe_data['time']."\", \"".$safe_data['remote_address']."\", \"".$safe_data['domain']."\", ".$safe_data['response_ip'].", ".$safe_data['response_code'].", ".$safe_data['response_status'].", ".$safe_data['response_time'].");";

//			$db->query($sql);
//		};
//		$db->disconnect();
//	};
};
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>

	<title><?php echo $title . " // isitup.org"; // display the dynamic title ?></title>

<?php if ($id == 0): ?>
	<meta name="robots" content="noindex" />
<?php endif; ?>
	<meta name="description" content="The availability results for <?php echo $domain; ?>. // isitup.org" />
	<meta name="keywords" content="is it up, isitup, is it up?, is <?php echo $domain; ?> up?, is <?php echo $domain; ?> down?, is it up website monitor, is it up website, is it down, is it just me, is it up yet" />

	<link rel="icon" type="image/png" href="<?php echo $setting["static"]; ?>/img/icon.png" />
	<link rel="stylesheet" type="text/css" media="screen, print" href="<?php echo $setting["static"]; ?>/css/reset.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $setting["static"]; ?>/css/style.css" />
	<link rel="stylesheet" type="text/css" media="print" href="<?php echo $setting["static"]; ?>/css/print.css" />

	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>
	<script type="text/javascript">
	/* <![CDATA[ */
	$(document).ready(function () {
		$("a").click(function() { this.blur(); });
	});
	/* ]]> */
	</script>
</head>
<body>
<div id="container">
	<?php echo $html; // displays the response for the site we're checking ?>

<?php if ($id != 0 && $domain != "isitup.org" && $domain != "127.0.0.1"): ?>
	<!--<p id="ad"><?php echo display_ad(1); ?></p>-->

<?php endif; ?>
</div>
</body>
</html>