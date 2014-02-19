<?php
/**
 * Generates a domain, with or without a port.
 *
 * @param   string  $domain
 * @param   int     $port
 *
 * @return  string
 */
function gen_domain($domain, $port)
{
    if ($port == 80 || !isset($port))
    {
        $url = $domain;
    }
    else
    {
        $url = $domain .":". $port;
    };
    
    return $url;
}

/**
 * Checks that $domain is a valid domain or ip.
 *
 * @param   string $domain
 *
 * @return  bool
 */
function is_valid_domain($domain)
{
    $domain_regex   = "/^([\w\d](-*[\w\d])*)\.(([\w\d](-*[\w\d])*))*$/i";

    return (filter_var($domain, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) != false || preg_match($domain_regex, $domain));
}

/**
 * Filters the given domain and split it into an array with a port number.
 *
 * @param   string  $domain
 *
 * @return  array   The domain and the port number.
 */
function filter_domain($domain)
{
    $domain = preg_replace("/[^A-Za-z0-9-\/\.\:]/", "", trim($domain));

    // Split the variable into two, $domain & $port.
    $result = explode(":", $domain);

    // If the port is not numeric or not set we use port 80.
    if (!isset($result[1]) || !is_numeric($result[1]))
    {
        $result[1] = 80;
    }

    return $result;
}

/**
 * Gets the http status code from $domain, using $port.
 *
 * @param   string  $domain
 * @param   int     $port
 *
 * @return  array
 */
function get_response($domain, $port)
{
    global $setting;
    
    if ( is_valid_domain($domain) )
    {        
        $options = array
        (
            'timeout' => $setting["timeout"],
            'useragent' => "Is is up?/" . $setting['version'] . " (http://isitup.org)",
            'referer' => "http://isitup.org",
            'port' => $port,
            'compress' => true
        );
        
        $r = @http_head("http://" . $domain, $options, $headers);

        $data = array
        (
            "code" => $headers["response_code"],
            "time" => $headers["connect_time"],
            "valid" => true
        );
    }
    else
    {
        $data = array
        (
            "code" => null,
            "time" => 0,
            "valid" => false
        );
    }
    
    return $data;
}

/**
 * Generates an id from $code.
 *
 * @param   int $code
 *
 * @return  int
 */
function gen_id($data)
{
    $good_codes = array(200, 301, 302, 303, 304, 307, 400, 401, 403, 405);

    if ($data["valid"] === false)
    {
        $id = 3;
    }
    else if (is_numeric($data["code"]) && in_array($data["code"], $good_codes))
    {
        $id = 1;
    }
    else
    {
        $id = 2;
    };
    
    return $id;
}

/**
 * Generates the page title.
 *
 * @param   int     $id
 * @param   string  $domain
 *
 * @return  string
 */
function gen_title($id, $domain)
{
    switch ($id)
    {
        case 3:
            return "Woops...";

        case 1:
            return "Yay, " . $domain . " is up.";

        case 2:
            return "Oh no! " . $domain . " is down.";
    }
}

/**
 * Generates the html for display.
 *
 * @param   int     $id
 * @param   string  $domain
 * @param   int     $port
 * @param   int     $time
 * @param   int     $code
 *
 * @return  string
 */
function gen_html($id, $domain, $port, $time, $code)
{
    $units = gen_units($time);

    if ( $time < 1 )
    {
            $time = $time * 1000;
    }
    else
    {
        $time = round($time, 2);
    }

    if ( $time <= 0 ) $time = "< 1";

    if ( $id == 1 )
    {
        $html  = "<p><a href=\"http://" . gen_domain($domain, $port) . "\" class=\"domain\" title=\"http://" . $domain . "/\" rel=\"nofollow\">" . $domain . "</a> is up.</p>\n\n";

        $html .= "\t<p class=\"smaller\">It took " . $time . " " . $units . " for a " . gen_http_wiki_link($code) . " response code" . show_ip($domain) . ".</p>\n\n";

        $html .= "\t<p class=\"smaller\"><a href=\"/\" title=\"Home\">Go back</a> to check another site" . gen_save($domain) . ".</p>\n";
    }
    else if ( $id == 2 )
    {
        if (!empty($code) && is_numeric($code))
        {
            $text = "We got a " . gen_http_wiki_link($code) . " response code" . show_ip($domain) . ".";
        };

        $html = "<p><a href=\"http://" . gen_domain($domain, $port) . "\" class=\"domain\" title=\"http://" . $domain . "/\" rel=\"nofollow\">" . $domain . "</a> seems to be down!</p>\n\n";
        
        if ( isset($text) )
        {
            $html .= "\t<p class=\"smaller\">" . $text . "</p>\n";
        };

        $html .= "\t<p class=\"smaller\"><a href=\"/\" title=\"Home\">Go back</a> to check another site" . gen_save($domain) . ".</p>\n";
    }
    else if ( $id == 3 )
    {
        $html  = "<p>We need a valid domain to check! <a href=\"/d/" . gen_domain($domain, $port) . "\">Try again.</a></p>\n";
    };

    if ($domain == "isitup.org" || $domain == "127.0.0.1")
    {
        $html  = "<p>Have a think about what you've just done and <a href=\"/\" title=\"Better luck next time.\">try again.</a></p>\n";
    };

    return $html;
}

/**
 * Generates a link to wikipedia.
 *
 * @param   int     $code
 *
 * @return  string
 */
function gen_http_wiki_link($code)
{
    switch ($code)
    {
        case ($code >= 500):
            $anchor = "5xx_Server_Error";
            break;

        case ($code >= 400):
            $anchor = "4xx_Client_Error";
            break;
            
        case ($code >= 300):
            $anchor = "3xx_Redirection";
            break;
            
        case ($code >= 200):
            $anchor = "2xx_Success";
            break;
            
        case ($code >= 100):
            $anchor = "1xx_Informational";
            break;
    }

    return "<a href=\"http://en.wikipedia.org/wiki/List_of_HTTP_status_codes#" . $anchor . "\" title=\"Wikipedia - HTTP Status Codes\">" . $code . "</a>";
}

/**
 * Generates the units for the response time.
 *
 * @param   int     $time
 *
 * @return  string
 */
function gen_units($time)
{
    switch ($time)
    {
        case 1:
            return "second";

        case ($time > 1):
            return "seconds";
        
        default:
            $units = "<a title=\"Milliseconds\" href=\"http://www.wolframalpha.com/input/?i=" . $time * 1000 . "%20milliseconds\">ms</a>";
            break;
    }
    
    return $units;
}

/**
 * Generates the save link.
 *
 * @param   string  $domain
 * @param   array   $default
 *
 * @return  bool|string
 */
function gen_save($domain)
{
    global $setting;

    $array = array();

    if ( isset($_COOKIE["input"]) )
    {
        $array[] = preg_replace("/[^A-Za-z0-9-\/\.\: ]/", "", trim($_COOKIE["input"]));
    };

    $array[] = $setting["default"];

    if ( !in_array($domain, $array) )
    {
        return " or <a href=\"/save/" . $domain . "\" title=\"Use " . $domain . " as the default site to check\">save</a> this as your default";
    };

    return false;
}

/**
 * Gets the ip address of the domain we're checking.
 *
 * @param   string $domain
 *
 * @return  string
 */
function show_ip($domain)
{
    $domexprcheck = "/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$/i";
    $ip = gethostbyname($domain);

    if (preg_match($domexprcheck, $domain) == true)
    {
        $text = " with an ip of <a href=\"http://" . $ip . "/\" title=\"http://" . $ip . "/\">" . $ip . "</a>";
    }
    else
    {
        $text = null;
    }
    
    return $text;
}

/**
 * Gets $cookie and turns it into an array
 *
 * @param   string $cookie
 *
 * @return  bool|array
 */
function get_cookie($cookie)
{
    if ( isset($_COOKIE[$cookie]) )
    {
        $clean = stripslashes(preg_replace("/[^A-Za-z0-9-\.\,\: ]/", "", trim($_COOKIE[$cookie])));

        return $clean;
    };
    
    return false;
}

/**
 * Sets a cookie.
 *
 * @param   string $name
 */
function set_cookie($name, $value)
{
    setcookie($name, $value, time() + 60 * 60 * 24 * 365, "/", $_SERVER["SERVER_NAME"], 0, 1);
}

/**
 * Removes one or more cookies.
 *
 * @param   array|string $cookie
 *
 * @return  bool
 */
function remove_cookies($cookie = array())
{
    if ( is_array($cookie) )
    {
        foreach ($cookie as $value)
        {
            setcookie($value, false, time() - 60 * 60 * 24 * 365, "/", $_SERVER["SERVER_NAME"], 0, 1);
        };
    }
    else
    {
        setcookie($cookie, false, time() - 60 * 60 * 24 * 365, "/", $_SERVER["SERVER_NAME"], 0, 1);
    };
    
    return false;
}

/**
 * Gets the correct input value for the homepage.
 *
 * @param   string  $a the remote domain
 * @param   array   $b the default domain
 *
 * @return  string
 */
function get_domain($remote, $default)
{

    $domain = $default;

    if (!isset($_GET["clear"]) && (!empty($_GET["d"]) || !empty($_COOKIE["input"])))
    {
        $domain = $remote;
    }
    
    return $domain;
}

/**
 * Gets the correct value for the input_val javascript variable on the homepage.
 *
 * @param   string  $a the remote domain
 * @param   array   $b the default domain
 *
 * @return  string
 */
function get_jsdomain($remote, $default)
{
    if (isset($_GET["save"]) && isset($_GET["d"]))
    {
        $domain = $remote;
    }
    else if (isset($_GET["clear"]))
    {
        $domain = $default;
    }
    else if (!empty($_COOKIE["input"]) && empty($_GET["d"]))
    {
        $domain = $remote;
    }
    else if (isset($_GET["d"]) && !isset($_GET["save"]))
    {
        $domain = null;
    }
    else
    {
        $domain = $default;
    };

    return $domain;
}

/**
 * Tests if a clear link should be shown or not.
 *
 * @return  bool
 */
function show_clear()
{
    if (isset($_COOKIE["input"])
            && !isset($_GET["clear"])
        || isset($_GET["save"])
            && isset($_GET["d"])
        || isset($_COOKIE["domains"])
            && !isset($_GET["clear"]))
    {
        return true;
    }
    
    return false;
}

/**
 * Generates the output for the api request.
 *
 * @param   array   $data
 * @param   string  $output The requested output type.
 *
 * @return  string  The api result.
 */
function gen_api($data, $output)
{
    switch ($output)
    {
        case "txt":
            return gen_txt($data);

        case "json":
            return gen_json($data);

        default:
            return false;
    }
}

/**
 * Generates the json output for an api request.
 *
 * @param   array   $data
 *
 * @return  string  The api result as json.
 */
function gen_json($data)
{
    // Allow the use of this json on any domain.
    header('Access-Control-Allow-Origin: *');
    
    $data = tidy_array($data, null);
        
    $json = format_json(json_encode($data));
        
    // JSONP callback function
    if ( isset($_GET["callback"]) )
    {      
        header('Content-Type: application/javascript; charset=utf-8');

        $json = safe_callback($_GET["callback"]) . "(" . $json . ");";
    }
    else
    {
        header('Content-Type: application/json; charset=utf-8');
    }
    
    return $json;
}

/**
 * Generates the text output for an api request.
 *
 * @param   array   $data
 *
 * @return  string  The api result as csv.
 */
function gen_txt($data)
{
    header('Content-Type: text/plain');
    
    $data = tidy_array($data, "NULL");
    
    $txt = "";
    
    $last_key = end(array_keys($data));
    
    foreach ($data as $key => $value)
    {
        $txt = $txt . $value;
    
        if ($key != $last_key) $txt = $txt . ", ";
    }
    
    return $txt;
}

/**
 * Replaces empty values from an array with a given value.
 *
 * @param   array   $array
 * @param   char    $replacement_value
 *
 * @return  array   The cleaned array.
 */
function tidy_array($array, $replacement_value)
{
    foreach ($array as &$value)
    {
        if ( empty($value) ) $value = $replacement_value;
    };
    
    unset($value);
    
    return $array;
}

/**
 * Formats the JSON response, adding whitespace for readability.
 *
 * @param   string  $json
 *
 * @return  string  The formatted JSON.
 */
function format_json($json)
{
    $pattern = array(',"', '{', '}', ':');

    $replacement = array(",\n    \"", "{\n    ", "\n}", ': ');
        
    $formatted_json = str_replace($pattern, $replacement, $json);

    return $formatted_json;
}

/**
 * Ensures the callback cannot be used for ~bad things~.
 *
 * @param   string  $input
 *
 * @return  string  The safe callback method.
 */
function safe_callback($input)
{
    $output = strip_tags($input);

    $chars = array(";", "(", ")", "\"", "\'");

    $output = str_replace($chars, "", $output);

    return $output;
}
