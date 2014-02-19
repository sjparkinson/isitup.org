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
    ();

    // A list of banned referrers, can include wildcards.
    $setting["banned_referrers"] = array
    ();

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

// Get and set the git revision number.
$setting["version"] = get_version();

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
 * Gets the short sha hash of the current git version if any.
 *
 * @return  string
 */
function get_version()
{
    // Get and set the git revision number.
    exec("git log -1 --pretty=format:'%h'", $version);

    if ( empty($version) )
    {
        return "Unversioned";
    }

    return $version[0];
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
if ($setting["live"] === false
    && $_SERVER["SCRIPT_NAME"] != "offline.php")
{
    header("Location: http://" . $setting["host"] . "/offline", true, 503);
    exit();
};

/**
 * Set the headers.
 */
header("X-XSS-Protection: 1; mode=block");

header("Via: Is it up?/" . $setting["version"] . " (" . gethostname() . "." . $_SERVER["HTTP_HOST"] . ")");
