<?php

use pte\Pte;

include 'vendor/autoload.php';

$pte = new Pte();
$pte->SetMaster('template/master.html');
$pte->SetHtml('template/view.html');

$pte->SetValue(array(
    'FirstCircle' => 'Selamat Datang !',
    'WishList' => array(
        array(
            'Lingkaran' => 'Bulat Sempurna 1',
            'Anak' => array(
                array(
                    'Umur' => 20,
                    'Anak2' => array(
                        array('Umur2' => 41),
                        array('Umur2' => 42),
                        array('Umur2' => 43),
                    ),
                ),
                array(
                    'Umur' => 19,
                    'Anak2' => array(
                        array('Umur2' => 14),
                        array('Umur2' => 15),
                        array('Umur2' => 16),
                    ),
                )
            ),
        ),
        array(
            'Lingkaran' => 'Bulat Sempurna 2',
            'Anak' => array(
                'Umur' => 46,
                'Anak2' => array(
                    array('Umur2' => 9),
                ),
            ),
        ),
    ),
    'Wishlist2' => array(
        'val' => 'DARI CONTROLLER',
    ),
    'navilera1' => 'GFriend Band',
    'Umur' => 23,
    'Author' => 'Didit Velliz',
    'Member' => array(
        'NamaMember' => 'Asus A451LB',
        'Alamat' => 'JL Sukamekar 2 no 14',
        'Hobi' => array(
            array('List' => 'renang'),
            array('List' => 'mancing'),
            array('List' => 'koding'),
        )
    ),
    'NamaMember' => 'Asus A451LB LUAR',
));

$pte->Output();