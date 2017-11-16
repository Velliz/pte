<?php

use pte\Pte;

include 'vendor/autoload.php';

$pte = new Pte();
$pte->SetMaster('template/master.html');
$pte->SetHtml('template/view.html');

$pte->SetValue(array(
    'Lingkaran1' => 'XYZ 123',
));

$pte->Output();