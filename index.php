<?php

//TODO: index will moved to pte

use pte\fruits\Fruits;
use pte\slicer\Slicer;

include 'vendor/autoload.php';

$template = new Fruits();
//$template->SetFruitMaster('template/plainmaster.html');
//$template->SetFruitBody('template/plain.html');
$template->SetFruitMaster('template/master.html');
$template->SetFruitBody('template/view.html');
//$template->AddFruitSegments('template/sidebar.html');
//echo $template->GetFruitPack();
//die();

$slicer = new Slicer();
/*
$data['Tests'] = "testingnya";
$data['Check'] = array(
    'Pass' => 'test pass',
    'Bts' => array(
        array('Pass' => 'Jin'),
        array('Pass' => 'Kevin'),
        array('Pass' => 'Bob'),
    ),
);
*/
$output = $template->GetFruitPack();
$parsedContent = $slicer->Lexer($output);

var_dump($parsedContent);