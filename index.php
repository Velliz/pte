<?php

use pte\Pte;

include 'vendor/autoload.php';

$pte = new Pte();
$pte->SetMaster('template/master.html');
$pte->SetHtml('template/view.html');

$pte->SetValue(array(
    'FirstCircle' => 'Selamat Datang !',
    'Check' => array(
        array(
            'WishList' => array(
                'Lingkaran' => 'Bulat Sempurna',
                'Anak' => array(
                    'Umur' => 19,
                    'Anak' => array(
                        'Umur' => 4,
                    ),
                ),
            ),
        ),
    ),
    'navilera1' => 'GFriend Band',
    'Umur' => 23
));

$pte->Output();