<?php

use pte\CustomRender;
use pte\Pte;

include 'vendor/autoload.php';

class BaseUrl implements CustomRender
{

    var $fn;
    var $param;

    var $tempJs = '';
    var $tempCss = '';

    public function Parse()
    {
        if ($this->fn === 'url') {
            return 'http://localhost/pte' . $this->param;
        }
        return '';
    }

    /**
     * @param $fnName
     * @param $paramArray
     */
    public function RegisterFunction($fnName, $paramArray)
    {
        $this->fn = $fnName;
        $this->param = $paramArray;
    }

    /**
     * @param $assets
     */
    public function RegisterAssets($assets)
    {
        // TODO: Implement RegisterAssets() method.
    }
}

$pte = new Pte(false, true, true);
$pte->SetMaster('template/master.html');
$pte->SetHtml('template/view.html');

$v = new BaseUrl();

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
));

$pte->Output(null, Pte::VIEW_HTML, array(
    'template/sidebars.html'
));

echo $pte->getElapsedTime();

