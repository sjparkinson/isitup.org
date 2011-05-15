<?php
header('HTTP/1.1 503 Service Unavailable');
require_once("settings.php");
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	<title>Is it up? - Offline</title>

	<meta name="description" content="We're currently offline for maintenance, please give us a couple of minutes to finish." />
	<meta name="keywords" content="is it up, isitup, is it up?, is it up website monitor, is it up website, is it down, is it just me" />
	<meta name="robots" content="noindex" />
	<meta http-equiv="refresh" content="30;url=http://<?php echo $setting["host"]; ?>/" />
	
	<link href='http://fonts.googleapis.com/css?family=PT+Sans' rel='stylesheet' type='text/css' />
	
	<?php mint_js(); ?>

	<link rel="icon" type="image/png" href="<?php echo $setting["static"]; ?>/img/icon.png" />
	<style type="text/css">
		/* Reset */
		html { color:#000; background: #FFF }
		body,div,h1,h2,h3,h4,h5,h6,form,input,p { margin:0; padding: 0 }
		img { border: 0 }
		h1 { font-size:100%; font-weight: normal }
		body { *font-size: small; *font: x-smallfont: 13px/1.231 arial, helvetica, clean, sans-serif }
		body { margin: 10px }
		h1 { font-size: 138.5% }
		h1 { margin: 1em 0 }
		h1,strong { font-weight: bold }
		p { margin-bottom: 1em }
		body {
			color: #36393D;
			font-family: 'PT Sans', Helvetica, Arial, Verdana, sans-serif;
			font-size: 100%; }

		/* Styles */
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