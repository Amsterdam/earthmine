<?php
include 'vars.inc.php';

$lat = isset($_GET['lat']) ? $_GET['lat'] : 0;
$lon = isset($_GET['lon']) ? $_GET['lon'] : 0;

$id = isset($_GET['id']) ? $_GET['id'] : null;

$secret = getenv('EARTHMINE_SECRET');
$key = getenv('EARTHMINE_KEY');
$time = time();
$sig = hash('md5', $key . $secret . $time);

$url = "http://cloud.earthmine.com/service?sig={$sig}&timestamp={$time}";

if ($id) {
    $parameters = [
        "operation" => "get-panoramas",
        "parameters" => [
            "request" => [
                "panorama-ids" => [ $id ]
            ]
        ]
    ];
} else {
    $parameters = [
        "operation" => "get-panoramas",
        "parameters" => [
            "request" => [
                "subject-location" => [
                    "lat" => $lat,
                    "lon" => $lon
                ],
                "search-radius" => 750, // From previous specs.
                "max-results" => "1" // Nearest panorama.
            ]
        ]
    ];
}
$parametersJson = json_encode($parameters);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $parametersJson);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($parametersJson),
    'x-earthmine-auth-id: ' . $key
));
$resultJson = curl_exec($ch); //FIXME Needs error recovery?
curl_close($ch);

$result = json_decode($resultJson);

if (empty($result->result->panoramas)) {
    exit; //FIXME
}

header("Access-Control-Allow-Origin: *");
header("Content-type: application/json");
echo json_encode($result->result->panoramas[0]);