<?php

$id = $_GET["id"];
$f = $_GET["f"];
$z = $_GET["z"];
$x = $_GET["x"];
$y = $_GET["y"];

$id_0 = substr($id, 0, 3);
$id_1 = substr($id, 3, 3);
$id_2 = substr($id, 6, 3);

header("Access-Control-Allow-Origin: *");
header("Content-type: image/jpeg");
readfile("http://s3.earthmine.com/tile/{$id_0}/{$id_1}/{$id_2}/{$id}/{$f}/z_{$z}/{$y}{$x}.jpg");