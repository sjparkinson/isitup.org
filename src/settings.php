<?php

    ############# Configuration #############

    // Set to false to bring the site offline.
    $setting["live"] = true;

    // Set to true to see PHP errors, warnings and notices.
    $setting["errors"] = false;

    // Server time zone, for database records. (use: http://www.php.net/manual/en/timezones.php)
    $setting["time_zone"] = "Europe/London";

    // The default input value
    $setting["default"] = "duckduckgo.com";

    // The max time to check if a site is working for, in seconds.
    $setting["timeout"] = 3;

    // Static content override. No trailing slash.
    // For example:
    // $setting["static"] = "http://my.cdn.com/static/";
    $setting["static"] = "/static"; // Local folder

    // Folder of script, no trailing slash.
    $setting["folder"] = "";

    // A list of banned user agents.
    $setting["banned_ua"] = array
    (
        "Mozilla/4.0 (compatible; ICS)",
        "Rackspace Monitoring/1.1 (https://monitoring.api.rackspacecloud.com)",
        "Python-urllib/1.17",
        "Xenu Link Sleuth/1.3.8"
    );

    // A list of banned referrers, can include wildcards.
    $setting["banned_referrers"] = array
    (
        "http://*.xnrg.net/*",
        "http://banjia.yulewangzhan.cn/*",
        "http://gang.yulewangzhan.cn/*",
        "http://hao.dangqian.com/hao/*",
        "http://tlapple.com/*",
        "http://www.010bjanmo.com/*",
        "http://www.36963.com/*",
        "http://www.510532.com/*",
        "http://www.51qqq.net/*",
        "http://www.62idc.com/*",
        "http://www.72tui.com/*",
        "http://www.110.gd*",
        "http://www.278cc.com/*",
        "http://www.598yingxiao.com/*",
        "http://www.680.com/*",
        "http://www.41418.net/*",
        "http://www.668108.com/",
        "http://www.747474.net/*",
        "http://www.2011522.com/*",
        "http://www.babaw.com/*",
        "http://www.bjnanmo.com/*",
        "http://www.craneceo.com/*",
        "http://www.dangqian.com/*",
        "http://www.dt-qz.com*",
        "http://www.ej158.com/*",
        "http://www.gpxz.com/*",
        "http://www.hexiushou.com/*",
        "http://www.hitsaati.com/backlinky.php",
        "http://www.itunion.cn/*",
        "http://www.junminqing.com/*",
        "http://www.ku58.com/*",
        "http://maskr.in/*",
        "http://www.ndhjd.com/*",
        "http://niulangdian.com/*",
        "http://www.pinyouge.com/*",
        "http://www.shihuifanli.com*",
        "http://www.ufukart.com/backlink/index.html",
        "http://www.wanshida518.cn/*",
        "http://www.yuehaiwang.com/*",
        "http://www.yuzhouzhiwang.com/*",
        "http://www.zjgdesign.com/*",
        "http://seo.dadadihao.com/*",
        "http://www.90kis.com/*",
        "http://www.zyruide.com/*",
        "http://www.bmizg.com/*",
        "http://www.ydjyjg.net/*"
    );

    #########################################

// Sets the error level.
if ($setting["errors"] == true)
{
    error_reporting(E_ALL);
}
else
{
    error_reporting(0);
}

// Sets the time zone.
date_default_timezone_set($setting["time_zone"]);

// Sets the timeout.
ini_set("default_socket_timeout", $setting["timeout"]);

// Sets the host domain.
$setting["host"] = $_SERVER["SERVER_NAME"];

if ($setting["folder"] != "")
{
    $setting["host"] = $setting["host"] . $setting["folder"];
}

/**
 * Checks the given request headers against the lists of banned items.
 *
 * @return  bool
 */
function is_bad_request()
{
    global $setting;

    // Check the referer is not banned.
    if (is_banned_referrer($setting["banned_referrers"])) return true;

    // Check the user-agent is not empty and not in the banned list.
    if (strlen(trim($_SERVER["HTTP_USER_AGENT"])) == 0
        || in_array($_SERVER["HTTP_USER_AGENT"], $setting["banned_ua"])) return true;

    return false;
}

/**
 * Checks the given referrer against the list of banned referrers and return true if there is a match.
 *
 * @return  bool
 */
function is_banned_referrer($patterns)
{
    // Check the supplied referrer isn't banned.
    if ( isset($_SERVER["HTTP_REFERER"]) )
    {
        foreach ($patterns as $pattern)
        {
            if ( fnmatch($pattern, $_SERVER["HTTP_REFERER"]) ) return true;
        }
    }

    return false;
}

// Forbid banned ips or user agents.
if ( is_bad_request() )
{
    header("HTTP/1.1 403 Forbidden");
    exit();
};

// Check if we should send people to the offline page.
if ($setting["live"] === false)
{
    header("Location: /offline.html", true, 503);
    exit();
};

/**
 * Set the headers.
 */
header("X-XSS-Protection: 1; mode=block");
