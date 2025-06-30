<?php
include_once __DIR__ . "/modules/config.php";
include_once __DIR__ . "/modules/get_proxy.php";
include_once __DIR__ . "/modules/ping.php";
include_once __DIR__ . "/modules/ipinfo.php";
include_once __DIR__ . "/modules/flag.php";

$final_data = [];
foreach ($sources as $source) {
    $final_data = array_merge($final_data, proxy_array_maker($source));
    $final_output = remove_duplicate($final_data);
}

file_put_contents(__DIR__ . "/proxy/mtproto.json", json_encode($final_output, JSON_PRETTY_PRINT));
?>
