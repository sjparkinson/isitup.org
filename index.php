<?php
// if the site is offline
require_once("settings.php");

if (in_array($_SERVER["REMOTE_ADDR"], $setting["banned_ips"]) || in_array($_SERVER["HTTP_USER_AGENT"], $setting["banned_ua"]) || empty($_SERVER["HTTP_USER_AGENT"])) {
	header('HTTP/1.1 403 Forbidden');
	exit(); };

if ($setting["live"] != true) {
	header('Location: http://' . $setting["host"] . '/offline');
	exit(); };

require_once("functions.php");

// data variable;
$domain = array();

if (isset($_GET["admin"])) {
	setcookie("admin", true, time() + 60 * 60 * 2, "/"); };

// set cookie, ?save
if (isset($_GET["save"]) && isset($_GET["d"])) {
	setcookie("input", $_GET["d"], time() + 60 * 60 * 24 * 365, "/"); };

// destroy cookie, ?clear
if (isset($_GET["clear"])) {
	remove_cookies(array("input", "custom"));
	header('Location: http://' . $setting["host"]);
	exit(); };

// set $remote_domain
if (isset($_GET["d"])) {
	$domain["remote"] = preg_replace("/[^A-Za-z0-9-\/\.\: ]/", "", trim($_GET["d"]));
} else if (isset($_COOKIE["input"])) {
	$domain["remote"] = preg_replace("/[^A-Za-z0-9-\/\.\: ]/", "", trim($_COOKIE["input"]));
} else {
	$domain["remote"] = null; };

$domain["cookie"] = get_cookie_array("custom");
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title>Is it up?</title>

	<!--
	    Developer:	Sam Parkinson (@r3morse)
	Dev. Homepage:	http://samp.im
	-->

	<meta name="description" content="A simple tool to check if a website or ip address is up or down." />
	<meta name="keywords" content="is it up, isitup, is it up?, it is up, website down, site down, is site down, is it down, is it just me" />

	<link rel="icon" type="image/png" href="<?php echo $setting["static"]; ?>/img/icon.png" />
	<link rel="stylesheet" type="text/css" media="screen, print" href="<?php echo $setting["static"]; ?>/css/reset.css" />
	<link rel="stylesheet" type="text/css" media="screen, print" href="<?php echo $setting["static"]; ?>/css/style.css" />
	<link rel="search" type="application/opensearchdescription+xml" title="Is it up?" href="/search.php" />
	
	<meta name="google-site-verification" content="MA2tkG9xZKSTYcSrShL-hBHN4m3Zct3mA4Yk8NMuwQU" />

	<!--[if lt IE 8]>
	<style type="text/css">
	#submit {
		position: relative;
		top: 8px; }
	</style>
	<![endif]-->
	
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script type="text/javascript">
	/* <![CDATA[ */
	$(document).ready(function () {
		// reset the form
		$("#submit").attr("disabled", false);
		$("#input").attr("disabled", false).css("color", "#AAA");

		var input = "<?php echo get_clear($domain["remote"], $setting["input"]); ?>";

		// clears the default input value on click
		$("#input").mousedown(function () {
			if ($(this).val() == input) {
				$(this).val("");
			}
		});
		
		// changes the colour of text input
		$("#input").focus(function () {
			$(this).css("color", "#36393D");
		});

		// we're submitting...
		$("#form").submit(function () {
			var url = $("#input").val();
			if (url != "") {
				$("#input").attr("disabled", true).css("color", "#AAA");
				$("#submit").attr("disabled", true);
				window.location = '/' + url.replace(/^[ \s]+|[ \s]+$|http(s)?:\/\/|\/(.*)/g, "");
			} else {
				$("#input").focus();
			}
			return false;
		});
		
		// json API link update
		$("#json").mouseover({api:"json"}, api_value);
		
		// txt API link update
		$("#txt").mouseover({api:"txt"}, api_value);
		
		function api_value(val) {
			val = val.data.api
			var domain = $("#input").val().trim();
			var regex  = /^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$/i;
			if (!domain.match(regex)) { domain = input; };
			$("#" + val).attr("href", domain + "." + val);
		};
	});
	/* ]]> */
	</script>
</head>
<body>
<div id="container">
	<noscript>
		<p class="warning"><img src="<?php echo $setting["static"]; ?>/img/exclamation.png" width="16" height="16" alt="" />Please don't enter "http://" or a trailing slash, as you do not seem to be using javascript.<br />i.e. <code>example.com</code> instead of <code>http://<u>example.com</u>/test/ing.html</code></p>
	</noscript>
<?php if ( isset($_GET["save"]) && empty($_GET["d"]) ) : ?>
	<p class="warning"><img src="<?php echo $setting["static"]; ?>/img/exclamation.png" width="16" height="16" alt="" /><i>error:</i> a domain needs to be selected, e.g. <code>http://isitup.org/?<u>d=example.com</u>&amp;save</code></p>
<?php endif; ?>
<?php if ( isset($_GET["d"]) && isset($_GET["save"]) ): ?>
	<p class="save"><img src="<?php echo $setting["static"]; ?>/img/accept.png" width="16" height="16" alt="" /><b><?php echo get_domain($domain["remote"], $setting["input"]); ?></b> is now your default domain. Click on <i>Clear</i> to restore the original.</p>
<?php endif; ?>

	<form method="get" action="check.php" id="form">
		<p>is <input type="text" name="domain" id="input" value="<?php echo get_domain($domain["remote"], $setting["input"]); ?>" accesskey="4" /> <input type="submit" id="submit" value="up?" accesskey="s" /></p>
	</form>
</div>

<div id="footer">
	<a href="http://github.com/r3morse/isitup" title="Is it up? on Github">Source</a> <span id="api">API: (<a href="/example.com.json" id="json" title="JSON API" rel="nofollow">.json</a>or<a href="/example.com.txt" id="txt" title="Text API" rel="nofollow">.txt</a>)</span> <?php if( test_clear() ): ?><a href="http://isitup.org/clear" title="Reset to the default settings">Clear</a> <?php endif; ?><a href="<?php echo $setting["static"]; ?>/txt/changelog.txt" id="version" title="Is it up? v<?php echo $setting["version"]; ?> Changelog">Changelog</a>
</div>
</body>
</html>
