<?php
$doc = new DOMDocument();
$doc->loadHTML("template/master.html");

$tags = $doc->getElementsByTagName('html');

foreach ($doc->childNodes as $item) {
    $domEl = new DOMElement();
    $domEl->
    var_dump($item);
}
