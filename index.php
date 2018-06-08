<?php

include 'vendor/autoload.php';

use pte\CustomRender;
use pte\Parts;
use pte\Pte;

class Sidebar extends Parts
{
    /**
     * @return string
     */
    public function Parse()
    {
        $this->pte->SetHtml('template/sidebar.html');
        $this->pte->SetValue($this->data);
        return $this->pte->Output(null, Pte::VIEW_HTML);
    }
}

class Base implements CustomRender
{

    var $tags;
    var $fnName;

    protected $assets;
    protected $data;
    protected $paramArray;

    /**
     * @return string
     */
    public function Parse()
    {
        if ($this->fnName === 'url') {
            return 'localhost' . $this->paramArray;
        }
        return '';
    }

    /**
     * @param $fnName
     * @param $paramArray
     */
    public function RegisterFunction($fnName, $paramArray)
    {
        $this->fnName = $fnName;
        $this->paramArray = $paramArray;
    }

    /**
     * @param $assets
     */
    public function RegisterAssets($assets)
    {
        $this->assets = $assets;
    }
}

$pte = new Pte(true, true, true);
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
    'namaband' => 'Didit Velliz Band',
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
    'NamaMember' => 'Asus A451LB',
    //the implementation of Parts abstract class
    'tables' => new Sidebar('tables', array('Tab' => 'XYZ')),
));

//the implementation of CustomRenderer interface
$base = new Base();
echo $pte->Output($base, Pte::VIEW_HTML, array(
    'template/sidebar.html'
));
echo $pte->GetElapsedTime();

