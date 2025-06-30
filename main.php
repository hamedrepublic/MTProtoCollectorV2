<?php
include_once "modules/config.php";
include_once "modules/get_proxy.php";
include_once "modules/ping.php";
include_once "modules/ipinfo.php";
include_once "modules/flag.php";

$final_data = [];
foreach ($sources as $source) {
    $final_data = array_merge($final_data, proxy_array_maker($source));
    $final_output = remove_duplicate($final_data); // ← اینم داخل get_proxy.php تعریف شده
}

file_put_contents("proxy/mtproto.json", json_encode($final_output, JSON_PRETTY_PRINT));
?>
