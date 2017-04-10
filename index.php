<?php
//TODO: index will moved to pte_environment
use pte\Baskets;
use pte\Fruits;
use pte\Slicer;

include 'vendor/autoload.php';

$data = array(
    'Author' => 'Didit Velliz',
    'Nama' => 'Puko Template Engine',
    'Title' => 'Test Template',
);

$template  = new Fruits();
$template->SetFruitMaster('template/master.html');
$template->SetFruitBody('template/view.html');
$template->SetFruitSegments('template/sidebar.html');

//echo $template->GetFruitPack();
$basket = new Baskets();

$slicer = new Slicer($template);
$slicer->Slices($basket);
$basket->Parser($data);

//var_dump($basket->GetBasket());
die();

foreach ($basket->GetBasket() as $k => $v) {
    var_dump($v->GetComponent());
    //echo ($v->Output());
}



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