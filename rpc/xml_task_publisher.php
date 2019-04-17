<?php

use Rpc\XmlRpcPublisher;

require_once __DIR__ . '/../vendor/autoload.php';

$rpc = new XmlRpcPublisher('request_server');

$xmlmsg = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<REQUEST type="get_pricelist">
    <OPTIONS/>
</REQUEST>
XML;

$get_as_attr = isset($argv[1]) && !empty($argv[1]) && $argv[1] === 'yes' ? $argv[1] : 'no';

$xml = new SimpleXMLElement($xmlmsg);

$xml->OPTIONS->addAttribute('get_as_attr', $get_as_attr);

//echo $xml->asXML();

try {
    $response = $rpc->call($xml->asXML());
} catch (ErrorException $e) {
    echo $e->getMessage();
}

echo $response;
