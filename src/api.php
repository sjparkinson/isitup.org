<?php

require("settings.php");
require("functions.php");

// Split the variable into two, $domain & $port.
list($domain, $port) = filter_domain($_GET["domain"]);

// Check the site and get the response code.
$data = get_response($domain, $port);

// Check if IDN domain - convert, display correct domain name in HTML
$domain = convert_idn_domain($domain);

// Split the code and data into seperate vars.
$code = $data["code"];

// Caluate and format the time taken to connect.
$time = round($data["time"], 3);

// Get the correct id for the result.
$id = gen_id($data);

// Orgnise and generate the output.
if ( isset($_GET["output"]) )
{
    // The API called, should be "txt" or "json".
    $type = $_GET["output"];

    $result = array
    (
        "domain"        => $domain,
        "port"          => $port,
        "status_code"   => $id,
        "response_ip"   => gethostbyname($domain),
        "response_code" => $code,
        "response_time" => $time
    );

    // Generate our output.
    $output = gen_api($result, $type);

    // If the api type was valid print our output.
    if ( $output != false ) echo($output);
};
