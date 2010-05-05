<?php
require_once("settings.php");
header('HTTP/1.1 503 Service Unavailable');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title>Is it up? - Offline</title>

	<meta name="description" content="We're currently offline for maintenance, please give us a couple of minutes to finish." />
	<meta name="keywords" content="is it up, isitup, is it up?, is it up website monitor, is it up website, is it down, is it just me" />
	<meta name="robots" content="noindex" />
	<meta http-equiv="refresh" content="30;url=http://<?php echo $setting["host"]; ?>/" />

	<link rel="icon" type="image/png" href="<?php echo $setting["static"]; ?>/img/icon.png" />
	<link rel="stylesheet" type="text/css" media="screen, print" href="<?php echo $setting["static"]; ?>/css/reset.css" />

	<style type="text/css">
		body {
			color: #36393D;
			font-family: Helvetica, "Helvetica LT Std", Arial, Verdana, sans-serif;
			font-size: 100%; }

		#container {
			width: 500px;
			margin: 0 auto;
			margin-top: 60px;
			text-align: left; }

			#container a,a:visited,a:active {
				color: #369;
				text-decoration: none; }

			#container a:hover { color: #4096EE; }

			#container h1 {
				font-size: 140%;
				color: #369;
				font-weight: normal;
				margin-top: 60px; }

			#container p {
				color: #36393D;
				text-align: justify;
				line-height: 1.5em; }
	</style>
</head>
<body>
<div id="container">
	<center><img src="<?php echo $setting["static"]; ?>/img/ajax-loader.gif" alt="" width="48px" height="48px" /></center>
	<h1>Humm... work is actually getting done!</h1>
	<p>Oh the irony, Is it up? is down. Please give us a few minutes to upgrade, we promise it'll be better than when you last used it.</p>
</div>
</body>
</html>