<?php
// if the site is offline
require_once("settings.php");

if (in_array($_SERVER["REMOTE_ADDR"], $setting["banned_ips"]) || in_array($_SERVER["HTTP_USER_AGENT"], $setting["banned_ua"]) || empty($_SERVER["HTTP_USER_AGENT"])) {
	header('HTTP/1.1 403 Forbidden');
	exit(); }

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

// check the site and get the response code
$data = get_response($domain, $port);

// split the code and data into seperate vars
$code = $data["code"];
$time = $data["time"];

// caluate and format the time taken to connect
$time	= round($time, 3);

if (preg_match("/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/i", gethostbyname($domain))) {
	$ip = gethostbyname($domain);
} else {
	$ip = "NULL"; };

$id		= gen_id($code);
$title	= gen_title($id, $domain);
$html	= gen_html($id, $domain, $port, $time, $code);

if (isset($_GET["output"])) {
	$output = $_GET["output"];
	
	$result = array(
			"domain"		=> $domain,
			"port"			=> $port,
			"status_code"	=> $id,
			"response_ip"	=> $ip,
			"response_code"	=> $code,
			"response_time"	=> $time );
	
	if ($output == "txt") {
	
		foreach ($result as &$value) {
			if (empty($value)) { $value = "NULL"; };
		};
		
		unset($value);

		header('Content-type: text/plain');
	
		// domain, id, ip, http_code, response_time
		print $result["domain"] . ", " . $result["port"] . ", " . $result["status_code"] . ", " . $result["response_ip"] . ", " . $result["response_code"] . ", " . $result["response_time"];
		
		exit();
	} elseif ( $output == "json" ) {
		
		foreach ($result as &$value) {
			if (empty($value)) { $value = null; };
		};

		unset($value);
		
		header('Content-type: application/json');
		
		print json_encode($result);
		
		exit();
	};
};
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>

	<title><?php echo $title . " // isitup.org"; // display the dynamic title ?></title>
	
	<meta name="robots" content="noindex" />

	<meta name="description" content="The availability results for <?php echo $domain; ?>. // isitup.org" />
	<meta name="keywords" content="is it up, isitup, is it up?, is <?php echo $domain; ?> up?, is <?php echo $domain; ?> down?, it is up, website down, site down, is site down, is it down, is it just me" />

	<link rel="icon" type="image/png" href="<?php echo $setting["static"]; ?>/img/icon.png" />
	<link rel="stylesheet" type="text/css" media="screen, print" href="<?php echo $setting["static"]; ?>/css/reset.css" />
	<link rel="stylesheet" type="text/css" media="screen, print" href="<?php echo $setting["static"]; ?>/css/style.css" />
</head>
<body>
<div id="container">
	<?php echo $html; // displays the response for the site we're checking ?>

<?php if ($id != 0 && $domain != "isitup.org" && $domain != "127.0.0.1"): ?>
	<?php echo display_ad(); ?>
<?php endif; ?>
</div>
</body>
</html>