<?php

require("settings.php");
require("functions.php");

// Split the variable into two, $domain & $port.
list($domain, $port) = filter_domain($_GET["domain"]);

// Check the site and get the response code.
$data = get_response($domain, $port);

// Caluate and format the time taken to connect.
$time = round($data["time"], 3);

$id     = gen_id($data);
$title  = gen_title($id, $domain);
$html   = gen_html($id, $domain, $port, $time, $data["code"]);

?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>

    <title><?php echo $title; // display the dynamic title ?></title>

    <!-- Meta Info -->
    <meta name="description" content="The availability results for <?php echo $domain; ?>." />
    <meta name="msapplication-TileImage" content="<?php echo $setting["static"]; ?>/img/icon.png">
    <meta name="msapplication-TileColor" content="#ECECEC">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="<?php echo $setting["static"]; ?>/img/small-icon.png" />
    <link rel="image_src" href="<?php echo $setting["static"]; ?>/img/icon.png" />

    <!-- Stylesheets -->
    <link rel="stylesheet" type="text/css" media="screen, print" href="<?php echo $setting["static"]; ?>/css/reset.css" />
    <link rel="stylesheet" type="text/css" media="screen, print" href="<?php echo $setting["static"]; ?>/css/style.css" />

    <?php if((strpos($_SERVER["HTTP_USER_AGENT"], "MSIE") !== false)): ?>
    <style type="text/css">
        #container .domain {
            display: inline-block;
            word-break: break-all;
        }
    </style>
    <?php endif; ?>

    <!-- Mobile Browser Stuff -->
    <meta name="viewport" content="width=device-width" />
</head>
<body>
<div id="share">
    <?php if ($id == 1 || $id == 2): ?>
    <script>
        (function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];

            if (!d.getElementById(id)) {
                js = d.createElement(s);
                js.id = id;
                js.src = "//platform.twitter.com/widgets.js";
                fjs.parentNode.insertBefore(js, fjs);
            }
        }(document, "script", "twitter-wjs"));
    </script>

    <a href="//twitter.com/share" class="twitter-share-button"
        data-size="large"  data-hashtags="isitup" data-dnt="true"
        data-dnt="true" data-related="r3morse"
        data-text="<?php echo $title; ?>"></a>
    <?php endif; ?>
</div>

<div id="container">
    <?php
        // displays the response for the site we're checking
        echo( $html ); 
    ?>
</div>
</body>
</html>
