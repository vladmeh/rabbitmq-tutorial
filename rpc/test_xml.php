<?php

require_once __DIR__ . '/../vendor/autoload.php';

if (file_exists(__DIR__.'/price_list_attr.xml')) {
    $xml = simplexml_load_file(__DIR__.'/price_list_attr.xml');

    echo $xml->PRICELIST->POSITION[0]['webdiscount'];
} else {
    exit('Не удалось открыть файл price_list.xml.');
}
