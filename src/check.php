<?php

require("settings.php");
require("functions.php");

// Split the variable into two, $domain & $port.
list($domain, $port) = filter_domain($_GET["domain"]);

// Check the site and get the response code.
$data = get_response($domain, $port);

// Check if IDN domain - convert, display correct domain name in HTML
$domain = convert_idn_domain($domain);

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

    <title><?php echo $title . " // isitup.org"; // display the dynamic title ?></title>

    <!-- Meta Info -->
    <meta name="description" content="The availability results for <?php echo $domain; ?>. // isitup.org" />

    <meta name="theme-color" content="#ECECEC">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="/static/img/icon.png" />

    <!-- Stylesheets -->
    <link rel="stylesheet" type="text/css" media="all" href="/static/css/reset.css" />
    <link rel="stylesheet" type="text/css" media="all" href="https://fonts.googleapis.com/css?family=Bebas+Neue&display=swap" />
    <link rel="stylesheet" type="text/css" media="screen" href="/static/css/style.css" />

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
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-41035960-4"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-41035960-4');
    </script>

    <link rel="preload" href="https://cdn.carbonads.com/carbon.js?serve=CKYI5K3I&amp;placement=isituporg" as="script">
    <link rel="preconnect" href="https://srv.carbonads.net" crossorigin>
    <link rel="preconnect" href="https://cdn4.buysellads.net" crossorigin>
</head>
<body>
<div id="share">
    <?php if ($id == 1 || $id == 2): ?>
    <a href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button"
        data-show-count="false"
        data-size="large"
        data-hashtags="isitup"
        data-dnt="true"
        data-text="<?php echo $title; ?>">Tweet</a>
    <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
    <?php endif; ?>
</div>

<div id="container">
    <?php
        // displays the response for the site we're checking
        echo( $html );
    ?>
</div>

<script async type="text/javascript" src="https://cdn.carbonads.com/carbon.js?serve=CKYI5K3I&amp;placement=isituporg" id="_carbonads_js"></script>

</body>
</html>
