<?php
// if the site is offline
require_once("settings.php");
if ($setting["live"] != true) {
	header('Location: http://' . $setting["host"] . '/offline');
	exit(); }

header('HTTP/1.1 404 Not found');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title>Oh noes, an error! // isitup.org</title>

	<link rel="icon" type="image/png" href="<?php echo $setting["static"]; ?>/img/icon.png" />
	<link rel="stylesheet" type="text/css" media="screen, print" href="<?php echo $setting["static"]; ?>/css/reset.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo $setting["static"]; ?>/css/style.css" />
	<link rel="stylesheet" type="text/css" media="print" href="<?php echo $setting["static"]; ?>/css/print.css" />
	<meta name="robots" content="noindex" />

</head>
<body>
<div id="container">
	<p>Oh dear! You've generated an error. <a href="http://<?php echo $setting["host"]; ?>/">Try again.</a></p>
</div>
</body>
</html>