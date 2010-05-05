<?php
// if the site is offline
require_once("settings.php");
if ($setting["live"] != true) {
	header('Location: http://' . $setting["host"] . '/offline');
	exit(); }

require_once("functions.php");

// data variable;
$domain = array();

// set cookie, ?save
if (isset($_GET["save"]) && isset($_GET["d"])) {
	setcookie("input", $_GET["d"], time() + 60 * 60 * 24 * 365, "/"); };

// destroy cookie, ?clear
if (isset($_GET["clear"])) { remove_cookies(array("input", "custom")); };

if (isset($_GET["admin"])) {
	setcookie("admin", true, time() + 60 * 60 * 2, "/"); };

// set $remote_domain
if (isset($_GET["d"])) {
	$domain["remote"] = preg_replace("/[^A-Za-z0-9-\/\.\: ]/", "", trim($_GET["d"]));
} else if (isset($_COOKIE["input"])) {
	$domain["remote"] = preg_replace("/[^A-Za-z0-9-\/\.\: ]/", "", trim($_COOKIE["input"]));
} else {
	$domain["remote"] = null; };

$domain["cookie"] = get_cookie_array("custom");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title>Is it up?</title>

	<!--
	    Developer:	Sam Parkinson (@r3morse)
	Dev. Homepage:	http://samp.im
	-->

	<meta name="description" content="A simple tool to check if a website or ip address is up or down." />
	<meta name="keywords" content="is it up, isitup, is it up?, is it up website monitor, is it up website, is it down, is it just me, is it up yet" />

	<link rel="icon" type="image/png" href="<?php echo $setting["static"]; ?>/img/icon.png" />
	<link rel="stylesheet" type="text/css" media="screen, print" href="<?php echo $setting["static"]; ?>/css/reset.css" />
	<link rel="stylesheet" type="text/css" media="screen, print" href="<?php echo $setting["static"]; ?>/css/style.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $setting["static"]; ?>/css/jquery.autocomplete.css" />
	<link rel="search" type="application/opensearchdescription+xml" title="Is it up?" href="<?php echo $setting["static"]; ?>/xml/search.xml" />

	<!--[if lt IE 8]>
	<style type="text/css">
	#submit {
		position: relative;
		top: 8px; }
	</style>
	<![endif]-->

	<style type="text/css">
	#footer {
		position: static;
		text-align: center;
		min-width: 600px; }
	</style>

	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $setting["static"]; ?>/js/compressed.js"></script>
	<script type="text/javascript">
	/* <![CDATA[ */
	$(document).ready(function () {
		// reset the form
		$("#submit").attr("disabled", false);
		$("#input").attr("disabled", false).css("color", "#AAA");

		var data	= ["<?php echo gen_auto_domains($domain["cookie"], $setting["auto_domains"]) ?>"];
		var input	= "<?php echo get_clear($domain["remote"], $setting["input"]); ?>";
		var select	= false;

		// browser back cache fix...
		$("body").attr("onunload", "");

		// gets rid of dotted outline around links
		$("a").click(function() {
			this.blur();
		});

		$("#input").blur(function () {
			select = false;
		});

		// add autocomplete to the form
		$("#input").autocomplete(data, {
			highlight: false
		}).result(function () {
			$("#form").submit();
		});

		// sort out the default input value
		$("#input").click(function () {
			if ($(this).val() == input) {
				$(this).val("").css("color", "#36393D");
			}
			if (!select) {
				$(this).css("color", "#36393D").select();
				select = true;
			}
		});

		// we're submitting...
		$("#form").submit(function () {
			var url = $("#input").val();
			if (url != "") {
				$("#input").attr("disabled", true).css("color", "#AAA");
				$("#submit").attr("disabled", true);
				$("#submit").blur();
				window.location = '/' + url.replace(/^[ \s]+|[ \s]+$|http(s)?:\/\/|\/(.*)/g, "");
			} else {
				$("#input").focus();
			}
			return false;
		});
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
		<p>is <input type="text" name="domain" id="input" value="<?php echo get_domain($domain["remote"], $setting["input"]); ?>" accesskey="4" /> <input type="submit" id="submit" value="up?" accesskey="s" /><a href="changelog.txt" id="version" title="Is it up? Changelog"><?php echo "v. " . $setting["version"]; ?></a></p>
	</form>
</div>

<div id="footer">
	by <a href="http://samp.im/" title="Sam Parkinson">Sam Parkinson</a> <a href="http://github.com/r3morse/isitup" title="Download the source code">Source</a> <?php if( test_clear() ): ?><a href="http://isitup.org/clear" title="Reset to the default settings">Clear</a><?php endif; ?>
</div>
</body>
</html>