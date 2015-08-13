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

    <title><?php echo $title . " // isitup.org"; // display the dynamic title ?></title>

    <!-- Hi r/ProgrammerHumor :wave:! -->

    <!-- Meta Info -->
    <meta name="description" content="The availability results for <?php echo $domain; ?>. // isitup.org" />

    <meta name="theme-color" content="#ECECEC">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="/static/img/icon.png" />

    <!-- Stylesheets -->
    <link rel="stylesheet" type="text/css" media="screen, print" href="/static/css/reset.css" />
    <link rel="stylesheet" type="text/css" media="screen, print" href="/static/css/style.css" />

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
    window.twttr = (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0],
        t = window.twttr || {};
        if (d.getElementById(id)) return t;
        js = d.createElement(s);
        js.id = id;
        js.src = "https://platform.twitter.com/widgets.js";
        js.async = true;
        fjs.parentNode.insertBefore(js, fjs);

        t._e = [];
        t.ready = function(f) {
            t._e.push(f);
        };

        return t;
    }(document, "script", "twitter-wjs"));
    </script>

    <a href="https://twitter.com/intent/tweet" class="twitter-share-button"
        data-size="large"  data-hashtags="isitup" data-dnt="true"
        data-dnt="true" data-related="samparkinson_"
        data-text="<?php echo $title; ?>"></a>
    <?php endif; ?>
</div>

<div id="container">
    <?php
        // displays the response for the site we're checking
        echo( $html );
    ?>
</div>

<div id="buysellads">
    <!-- BuySellAds.com Zone Code -->
    <script type="text/javascript" src="http://cdn.adpacks.com/adpacks.js?legacyid=1273050&zoneid=1386&key=7b6723eb9ed007b227c359abd99a3ca1&serve=C6SI42Y&placement=isituporg&circle=dev" id="_adpacks_js"></script>
    <!-- End BuySellAds.com Zone Code -->
</div>
</body>
</html>
