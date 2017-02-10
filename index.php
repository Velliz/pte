<?php

include 'vendor/autoload.php';

$template  = new \pte\Fruits();
$template->SetFruitMaster('template/master.html');
$template->SetFruitBody('template/view.html');
$template->SetFruitSegments('template/sidebar.html');

//echo $template->GetFruitPack();

$slicer = new \pte\Slicer($template);
$slicer->Slices();

/*
include 'template.php';

$template = new template();
$template->SetHtml('view.html');
$template->SetMaster('master.html');
$template->SetView('sidebar.html');
$template->SetValue(array());
$template->Output(true, template::HTML_TRUE, template::MASTER_TRUE);
*/