<?php
/**
 * Generates a domain, with or without a port.
 * @param	string	$domain
 * @param	int		$port
 * @return	string
 */
function gen_domain($domain, $port) {
	if ($port == 80 || !isset($port)) {
		$url = $domain;
	} else {
		$url = $domain .":". $port;	};
return $url; }

/**
 * Gets the ip address of the domain we're checking.
 * @param	string $domain
 * @return	string
 */
function show_ip($domain) {
	$domexprcheck = "/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$/i";
	$ip = gethostbyname($domain);

	if (preg_match($domexprcheck, $domain) == true) {
		$text = " with an ip of <a href=\"http://" . $ip . "\" title=\"" . $ip . "\">" . $ip . "</a>";
	} else {
		$text = null; }
return $text; }

/**
 * Checks that $domain is a valid domain or ip.
 * @param	string $domain
 * @return	bool
 */
function test_domain($domain) {
	// reg expression used to check if the domain or ip address is valid
	$domexprcheck	= "/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$/i";
	$ipexpcheck		= "/^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/i";

return (preg_match($domexprcheck, $domain) || preg_match($ipexpcheck, $domain)) ? true : false; }

/**
 * Generates the units for the response time.
 * @param	int		$time
 * @return	string
 */
function gen_units($time) {
	if ($time == 1) { $units = "second"; }
	elseif ($time > 1) { $units = "seconds"; }
	else { $units = "<abbr title=\"Milliseconds\">ms</abbr>"; }
return $units; }

/**
 * Gets the http status code from $domain, using $port.
 * @param	string	$domain
 * @param	int		$port
 * @return	mixed
 */
function get_response($domain, $port) {
	global $setting;
	if (test_domain($domain)) {
		// retrieves just the http header response
		/// old code: $headers = @get_headers("http://" . $domain . ":" . $port);
		$start = microtime(true);
		$r = @http_head("http://" . $domain . ":" . $port, array('timeout' => $setting["timeout"]), $headers);
		$end = microtime(true);

		$code = array("code" => $headers["response_code"],
					  "time" => $end - $start);
		if ($headers["response_code"] == 0) {
			return false; }
	} else {
		$code = array("code" => "invalid",
					  "time" => 0); }
return $code; }

/**
 * Generates the html for display.
 * @param	int		$id
 * @param	string	$domain
 * @param	int		$port
 * @param	int		$time
 * @param	int		$code
 * @return	string
 */
function gen_html($id, $domain, $port, $time, $code) {
$units = gen_units($time);
if ($time < 1) { $time = $time * 1000; } else { $time = round($time, 2); }
if ($time <= 0) { $time = "< 1"; }
	if ($id == 1) {
		$html  = "<p><a href=\"http://" . gen_domain($domain, $port) . "\" class=\"domain\" title=\"" . $domain . "\" rel=\"nofollow\">" . $domain . "</a> is working <span class=\"smile\">:)</span></p>\n\n";
		$html .= "\t<p class=\"smaller\">It took " . $time . " " . $units . " for a <a href=\"http://en.wikipedia.org/wiki/List_of_HTTP_status_codes\" title=\"Wikipedia - HTTP Status Codes\">" . $code . "</a> response" . show_ip($domain) . ".</p>\n\n";
		$html .= "\t<p class=\"smaller\">Check <a href=\"/\" title=\"Home\">another site</a>" . gen_save($domain) . ".</p>\n";
	} else if ($id == 2) {
		if (!empty($code) && is_numeric($code)) {
			$text = "We got a <a href=\"http://en.wikipedia.org/wiki/List_of_HTTP_status_codes\" title=\"Wikipedia - HTTP Status Codes\">" . $code . "</a> http status code" . show_ip($domain) . ".";
		};

		$html  = "<p><a href=\"http://" . gen_domain($domain, $port) . "\" class=\"domain\" title=\"" . $domain . "\" rel=\"nofollow\">" . $domain . "</a> seems to be down <span class=\"smile\">:(</span></p>\n\n";
		if (isset($text)) {
			$html .= "\t<p class=\"smaller\">" . $text . "</p>\n";
		};
		$html .= "\t<p class=\"smaller\">Check <a href=\"/\" title=\"Home\">another site</a>" . gen_save($domain) . ".</p>\n";
	} else if ($id == 0) {
		$html  = "<p>We need a valid domain to check! <a href=\"/d/" . gen_domain($domain, $port) . "\">Try again.</a></p>\n"; };

	if ($domain == "isitup.org" || $domain == "127.0.0.1") {
		$html  = "<p>Have a think about what you've just done. <a href=\"/\" title=\"Better luck next time.\">Try again.</a></p>\n"; };
return $html; }

/**
 * Generates the page title.
 * @param	int		$id
 * @param	string	$domain
 * @return	string
 */
function gen_title($id, $domain) {
	if ($id == 0) {
		$title = "Woops...";
	} else if ($id == 1) {
		$title = "Yep, " . $domain . " is up.";
	} else if ($id == 2) {
		$title = "Oh noes! " . $domain . " is down."; };
return $title; }

/**
 * Generates an id from $code.
 * @param	int $code
 * @return	int
 */
function gen_id($code) {
	$good = array(200, 301, 302, 303, 304, 307, 400, 401, 403, 405);

	if ($code == "invalid") {
		$id = 0;
	} else if (is_numeric($code) && in_array($code, $good)) {
		$id = 1;
	} else {
		$id = 2; };
return $id; }

/**
 * Gets $cookie and turns it into an array
 * @param	string $cookie
 * @return	bool|array
 */
function get_cookie_array($cookie) {
	if (isset($_COOKIE[$cookie])) {
		$clean = preg_replace("/[^A-Za-z0-9-\.\,\: ]/", "", trim($_COOKIE[$cookie]));
		$array = explode(",", (stripslashes($clean)));
		return $array; };
return false; }

/**
 * Generates a string to store in a cookie
 * @param	array $array
 * @return	bool|string
 */
function gen_cookie_string($array) {
	if (is_array($array)) {
		$cookie = implode(",", $array);
		return $cookie;	};
return false; }

/**
 * Gets the correct input value for the homepage.
 * @param	string	$a the remote domain
 * @param	array	$b the default domain
 * @return	string
 */
function get_domain($a, $b) {
	if (isset($_GET["clear"])) {
		$d = $b;
	} else if (!empty($_GET["d"]) || !empty($_COOKIE["input"])) {
		$d = $a;
	} else {
		$d = $b; };
return $d; }

/**
 * Gets the correct value for the javascript on the homepage.
 * @param	string	$a the remote domain
 * @param	array	$b the default domain
 * @return	string
 */
function get_clear($a, $b) {
	if (isset($_GET["save"]) && isset($_GET["d"])) {
		$d = $a;
	} else if (isset($_GET["clear"])) {
		$d = $b;
	} else if (!empty($_COOKIE["input"]) && empty($_GET["d"])) {
		$d = $a;
	} else {
		$d = $b; };
return $d; }

/**
 * Tests if a clear link should be shown or not.
 * @return	bool
 */
function test_clear() {
	return ((isset($_COOKIE["input"]) && !isset($_GET["clear"]) || isset($_GET["save"]) && isset($_GET["d"]) || isset($_COOKIE["domains"]) && !isset($_GET["clear"])) ? true : false); }

/**
 * Generates the save link.
 * @param	string	$domain
 * @param	array	$default
 * @return	bool|string
 */
function gen_save($domain) {
	global $setting;
	$array = array();

	if (isset($_COOKIE["input"])) {
		$array[] = preg_replace("/[^A-Za-z0-9-\/\.\: ]/", "", trim($_COOKIE["input"])); };

	$array[] = $setting["input"];

	if (!in_array($domain, $array)) {
		return " or save as your <a href=\"http://isitup.org/save/" . $domain . "\" title=\"Use " . $domain . " as the default site to check\">default</a>"; };
return false; }

/**
 * Generates the ad html.
 * @param	int	$ad the specific ad to display
 * @return	bool|string
 */
function display_ad($ad = 0) {
	$link[] = 'Free online storage with <a href="https://www.dropbox.com/referrals/NTQwNTI2ODk" target="_blank" title="Get Dropbox!">Dropbox</a>!';
	$link[] = 'Sync to the cloud with <a href="https://www.dropbox.com/referrals/NTQwNTI2ODk" target="_blank" title="Get Dropbox!">Dropbox</a>!';
	$link[] = 'Share files online with <a href="https://www.dropbox.com/referrals/NTQwNTI2ODk" target="_blank" title="Get Dropbox!">Dropbox</a>!';

	$seed = microtime(true);
	mt_srand($seed);

	if ($ad != 0) {
		return $link[$ad]."\n";
	} else {
		$adnum = mt_rand(0, count($link) - 1);
		return "<p id=\"ad\">" . $link[$adnum]."</p>\n"; };
return false; }

/**
 * Removes one or more cookies.
 * @param	array|string	$cookie
 * @return	bool
 */
function remove_cookies($cookie = array()) {
	if (is_array($cookie)) {
		foreach ($cookie as $value) {
			setcookie($value, "", time() - 60 * 60); };
	} else {
		setcookie($cookie, "", time() - 60 * 60); };
return false; }