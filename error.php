<?php
// if the site is offline
require_once("settings.php");
if ($setting["live"] != true) {
	header('Location: http://' . $setting["host"] . '/offline');
	exit(); }

header('HTTP/1.1 404 Not found');
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title>Oh noes, an error!</title>

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