<?php
include "modules/config.php";           // تعریف $sources
include "modules/get_proxy.php";        // توابع getProxies و proxy_array_maker
include "modules/ping.php";             // احتمالاً تابع ping
include "modules/ipinfo.php";           // اطلاعات آی‌پی
include "modules/flag.php";             // تابع getFlags

$final_data = [];
foreach ($sources as $source) {
    $final_data = array_merge($final_data, proxy_array_maker($source));
    $final_output = remove_duplicate($final_data); // ← اینم داخل get_proxy.php تعریف شده
}

file_put_contents("proxy/mtproto.json", json_encode($final_output, JSON_PRETTY_PRINT));
?>
