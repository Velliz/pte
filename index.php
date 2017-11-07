<?php

//TODO: index will moved to pte_environment

use pte\Baskets;
use pte\Fruits;
use pte\Slicer2;
use pte\Slicer;

include 'vendor/autoload.php';

$template = new Fruits();
$template->SetFruitMaster('template/master.html');
$template->SetFruitBody('template/view.html');
$template->SetFruitSegments('template/sidebar.html');
echo $template->GetFruitPack();
die();

$slicer = new Slicer($template);
$data['Tests'] = "testingnya";
$data['Check'] = array(
    'Pass' => 'test pass',
    'Bts' => array(
        array('Pass' => 'Jin'),
        array('Pass' => 'Kevin'),
        array('Pass' => 'Bob'),
    ),
);

$output = array(
    'child' => array()
);
$parsedContent = $slicer->Lexer($template);
var_dump($parsedContent);