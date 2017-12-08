<?php

include 'vendor/autoload.php';

use pte\elements\Tables;
use pte\Pte;

$pte = new Pte(false, true, true);
$pte->SetMaster('template/master.html');
$pte->SetHtml('template/view.html');

$v = new Tables('tables', array());

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
                )
            ),
        ),
        array(
            'Lingkaran' => 'Bulat Sempurna 2',
            'Anak' => array(
                array(
                    'Umur' => 46,
                    'Anak2' => array(
                        array('Umur2' => 9),
                    ),
                ),
            ),
        ),
    ),
    'Wishlist2' => array(
        'val' => 'DARI CONTROLLER',
    ),
    'namaband' => 'GFriend Band',
    'Umur' => 23,
    'Author' => 'Didit Velliz',
    'Member' => array(
        array(
            'NamaMember' => 'Asus A451LB',
            'Alamat' => 'JL Sukamekar 2 no 14',
            'Hobi' => array(
                array('List' => 'Berenang'),
                array('List' => 'Nyuci'),
                array('List' => 'Masak'),
            ),
        )
    ),
    'NamaMember' => 'Asus A451LB LUAR',
    'tables' => new Tables('tables', array())
));

$pte->Output(null, Pte::VIEW_HTML, array(
    'template/sidebar.html'
));

echo $pte->getElapsedTime();

