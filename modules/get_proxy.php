<?php
include "flag.php";
include "ipinfo.php";
include "ping.php";

function getProxies($channel)
{
    $all_proxies = [];

    // مرحله اول: گرفتن پراکسی از صفحه‌ی اصلی کانال
    $main_page = file_get_contents("https://t.me/s/" . $channel);

    // گرفتن لینک‌های پراکسی
    preg_match_all('#href="(.*?)/proxy\?(.*?)" target="_blank"#', $main_page, $matches1);
    preg_match_all('#class="tgme_widget_message_inline_button url_button" href="(.*?)/proxy\?(.*?)"#', $main_page, $matches2);

    // اضافه کردن پراکسی‌های صفحه اصلی
    if (!empty($matches1[0])) {
        foreach ($matches1[0] as $m) {
            $all_proxies[] = urldecode($m);
        }
    }
    if (!empty($matches2[0])) {
        foreach ($matches2[0] as $m) {
            $all_proxies[] = urldecode($m);
        }
    }

    // مرحله دوم: پیدا کردن لینک پست‌هایی که مربوط به topic هستند
    preg_match_all('/href="\/' . $channel . '\/(\d+)"/', $main_page, $post_ids);

    $unique_ids = array_unique($post_ids[1]);

    foreach ($unique_ids as $id) {
        // بارگذاری هر پست دسته‌بندی شده
        $topic_page = @file_get_contents("https://t.me/s/$channel/$id");
        if (!$topic_page) continue;

        preg_match_all('#href="(.*?)/proxy\?(.*?)" target="_blank"#', $topic_page, $topic_matches1);
        preg_match_all('#class="tgme_widget_message_inline_button url_button" href="(.*?)/proxy\?(.*?)"#', $topic_page, $topic_matches2);

        foreach ($topic_matches1[0] as $m) {
            $all_proxies[] = urldecode($m);
        }
        foreach ($topic_matches2[0] as $m) {
            $all_proxies[] = urldecode($m);
        }
    }

    // حذف تکراری‌ها و مرتب‌سازی
    $all_proxies = array_unique($all_proxies);
    sort($all_proxies);

    return $all_proxies;
}

?>
