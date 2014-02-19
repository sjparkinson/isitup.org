<?php

require_once("settings.php");

?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>Oh noes, an error! // isitup.org</title>

    <!-- Meta Info -->
    <meta name="msapplication-TileImage" content="<?php echo $setting["static"]; ?>/img/icon.png">
    <meta name="msapplication-TileColor" content="#ECECEC">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="<?php echo $setting["static"]; ?>/img/small-icon.png" />

    <!-- Stylesheets -->
    <link rel="stylesheet" type="text/css" media="screen, print" href="<?php echo $setting["static"]; ?>/css/reset.css" />
    <link rel="stylesheet" type="text/css" media="screen, print" href="<?php echo $setting["static"]; ?>/css/style.css" />

    <!-- Mobile Browser Stuff -->
    <meta name="viewport" content="width=device-width" />

    <!-- Prevent Crawlers from Indexing -->
    <meta name="robots" content="noindex" />
</head>
<body>
<div id="container">
    <p>Oh dear! You've generated an error. <a href="http://<?php echo $setting["host"]; ?>/">Try again.</a></p>
</div>
</body>
</html>