<?php

//TODO: index will moved to pte_environment

use pte\Baskets;
use pte\Fruits;
use pte\Slicer2;
use pte\Slicer3;

include 'vendor/autoload.php';

$template = new Fruits();
$template->SetFruitMaster('template/master.html');
$template->SetFruitBody('template/view.html');
$template->SetFruitSegments('template/sidebar.html');
//echo $template->GetFruitPack();

$slicer = new Slicer3($template);
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
$parsedContent = $slicer->Lexer($template, $output['child']);
var_dump($parsedContent);



/*
include 'template.php';

$template = new template();
$template->SetHtml('view.html');
$template->SetMaster('master.html');
$template->SetView('sidebar.html');
$template->SetValue(array());
$template->Output(true, template::HTML_TRUE, template::MASTER_TRUE);
*/

/*
$compile = new \pte\compiler\FruitPresent();

$nama = $compile->structure_variable_value('didit', 10);
$return = $compile->command_return($nama);
$fn = $compile->structure_function_public('getAge', null, $return);

echo $fn;
*/