<?php
include "flag.php";
include "ipinfo.php";
include "ping.php";

function getProxies($channel)
{
    $all_proxies = [];

    // دریافت HTML صفحه اصلی کانال
    $main_page = @file_get_contents("https://t.me/s/" . $channel);
    if (!$main_page) return [];

    // استخراج لینک‌های پراکسی از صفحه اصلی
    preg_match_all('#href="(.*?)/proxy\?(.*?)"#', $main_page, $matches_main);
    foreach ($matches_main[0] as $m) {
        $all_proxies[] = html_entity_decode($m);
    }

    // استخراج آیدی پست‌های موضوع‌بندی شده (topic posts)
    preg_match_all('/href="\/' . preg_quote($channel, '/') . '\/(\d+)"/', $main_page, $post_ids);
    $unique_ids = array_unique($post_ids[1]);

    // بررسی هر پست دسته‌بندی‌شده
    foreach ($unique_ids as $id) {
        $topic_page = @file_get_contents("https://t.me/s/$channel/$id");
        if (!$topic_page) continue;

        preg_match_all('#href="(.*?)/proxy\?(.*?)"#', $topic_page, $matches_topic);
        foreach ($matches_topic[0] as $m) {
            $all_proxies[] = html_entity_decode($m);
        }
    }

    return array_unique($all_proxies);
}

function proxy_array_maker($source)
{
    $proxies = getProxies($source);
    $result = [];

    foreach ($proxies as $proxy) {
        $parsed = parse_proxy($proxy, $source);
        if (!empty($parsed)) {
            $result[] = $parsed;
        }
    }

    return $result;
}

function remove_duplicate($array)
{
    $serialized = array_map('serialize', $array);
    $unique = array_unique($serialized);
    return array_map('unserialize', $unique);
}

function parse_proxy($proxy, $name)
{
    $proxy_array = [];
    $url = html_entity_decode($proxy);
    $parts = parse_url($url);

    if (!isset($parts['query'])) return [];

    $query_string = str_replace("amp;", "", $parts["query"]);
    parse_str($query_string, $query_params);

    if (!isset($query_params['server'])) return [];

    $server = $query_params['server'];

    // فیلتر کردن دامنه‌ها و IPهای نامعتبر
    if (filtered_or_not("https://" . $server)) return [];

    // بررسی موقعیت جغرافیایی
    $ip_data = function_exists("ip_info") ? ip_info($server) : null;
    $flag = isset($ip_data["country"]) ? (function_exists("getFlags") ? getFlags($ip_data["country"]) : "🏳️") : "🚩";

    $query_params["name"] = "@" . $name . "|" . $flag;

    $proxy_array = $parts;
    unset($proxy_array["query"]);
    $proxy_array["query"] = $query_params;

    return $proxy_array;
}

?>
