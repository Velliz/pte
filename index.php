<?php

use pte\Pte;

include 'vendor/autoload.php';

$pte = new Pte();
$pte->SetMaster('template/master.html');
$pte->SetHtml('template/view.html');

$pte->SetValue(array(
    'FirstCircle' => 'Selamat Datang !',
    'Check' => array(),
    'WishList' => array(
        'Lingkaran' => 'Bulat Sempurna',
        'Anak' => array(),
    ),
    'navilera1' => 'GFriend Band'
));

$pte->Output();