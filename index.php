<?php

use pte\Pte;

include 'vendor/autoload.php';

$pte = new Pte();
$pte->SetMaster('template/master.html');
$pte->SetHtml('template/view.html');

$pte->SetValue(array(
    'FirstCircle' => 'Selamat Datang !',
    'WishList' => array(
        'Lingkaran' => 'Bulat Sempurna',
        'Anak' => array(
            'Umur' => 19,
            'Anak' => array(
                'Umur' => 4,
            ),
        ),
    ),
    'Wishlist2' => array(
        'val' => 'WishList 2 Value',
    ),
    'navilera1' => 'GFriend Band',
    'Umur' => 23
));

$pte->Output();