<?php

function exitPlaceholder() {
    if (isset($_GET['dl'])) {
        throw new Exception("Error Processing Request", 1);
    } else {
        $placeholderImage = 'placeholder.png';
        header("Content-type: image/jpeg");
        header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
        header("Cache-Control: post-check=0, pre-check=0", false);
        readfile($placeholderImage);
    }
    exit;
}

function returnImage($image) {
    if (isset($_GET['dl'])) {
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"screenshot.jpg\"");
        echo $image;
    } else {
        header("Content-type: image/jpeg");
        echo $image;
    }
}

$lat = isset($_GET['lat']) ? $_GET['lat'] : 0;
$lon = isset($_GET['lon']) ? $_GET['lon'] : 0;

if (!$lon || !$lat) {
    exit();
}

$secret = $_ENV('EARTHMINE_SECRET');
$key = $_ENV('EARTHMINE_KEY');
$time = time();
$sig = hash('md5', $key . $secret . $time);

$url = "http://cloud.earthmine.com/service?sig={$sig}&timestamp={$time}";

$width = isset($_GET['width']) ? $_GET['width'] : 256;
$height = isset($_GET['height']) ? $_GET['height'] : 256;
$response = isset($_GET['response']) ? strtolower($_GET['response']) : 'image';

$parameters = [
    "operation" => "get-views",
    "parameters" => [
        "request" => [
            "view-subject" => [
                "lat" => sprintf('%.5f', $lat),
                "lon" => sprintf('%.5f', $lon)
            ],   
            "image-size" => [
                "width" => $width,
                "height" => $height
            ],
            "field-of-view" => "89.9",
            "search-radius" => 750, // From previous specs.
            "max-results" => "1" // Nearest image.
        ]
    ]
];
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

if ($response === 'json') {
    header("Access-Control-Allow-Origin: *");
    header("Content-type: application/json");
    echo $resultJson;

    exit();
}

if ($response === 'image') {
    $result = json_decode($resultJson);

    if (empty($result->result->views)) {
        exitPlaceholder();
    }

    $tryTimes = 1;

    for($i=0, $file_info = new finfo(FILEINFO_MIME_TYPE); $i<$tryTimes; $i++) {
        $imageUrl = $result->result->views[0]->url->href;

        $ch = curl_init($imageUrl);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $image = curl_exec($ch);
        curl_close($ch);

        $mime_type = $file_info->buffer($image);
        if ($mime_type === 'image/jpeg') {
            returnImage($image);
            exit();
        }
    }
}

exitPlaceholder();
