# PTE - Puko Templating Engine

PTE is a standalone templating engine build for performance and compatible for standalone use. PTE in action traverses a DOM tree into a PHP array (lexer) and then combining output with data specs (parser).

try it with composer command

```
composer require velliz/pte
```

then instance the object

```php
$pte = new Pte(true);
$pte->SetMaster('template/master.html');
$pte->SetHtml('template/view.html');

$v = new BaseUrl();

$pte->SetValue($v, array(
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

$pte->Output(Pte::VIEW_HTML, array(
    'template/sidebar.html'
));
```

BaseUrl()

```php
<?php

class BaseUrl implements \pte\CustomRender
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
    public function Register($fnName, $paramArray)
    {
        $this->fn = $fnName;
        $this->param = $paramArray;
    }
}
```

master.html

```html
<!DOCTYPE html>
<html>
<head>
    <base href="{!url(/)}">
    <title>Page {!FirstCircle}</title>
</head>
<body>
<div class="container">
    <h1>Welcome to site {!Pass}</h1>
    <p>Today will be preparation {!url(/test/sumber)}</p>
    Wish List 1
    <hr>
    <!--{!WishList}-->
        <p>Mulai terjemahan - {!Lingkaran}</p>
        <!--{!Anak}-->
            <div>Ini anak 1 umur - {!Umur}</div>
            <!--{!Anak2}-->
                <div>Ini anak 2 umur - {!Umur2}</div>
            <!--{/Anak2}-->
            <div>Ini ulangi anak 1 umur - {!Umur}</div>
        <!--{/Anak}-->
        <h1>Sekilan</h1>
    <!--{/WishList}-->
    <hr>
    <!--{!Wishlist2}-->
    <p>Aku Testing dulu Whistlist 2 {!val}</p>
    <!--{/Wishlist2}-->

    {CONTENT}

    band1: {!namaband}
    <!--{!Wishlist2}-->
    <p>Aku Testing dulu Whistlist 2 {!val}</p>
    <!--{/Wishlist2}-->
</div>

{!view(sidebar.html)}

{!part(css)}
{!part(js)}

</body>
</html>
```

view.html

```html
{!css(<link href="template/bootstrap.css" rel="stylesheet" type="text/css" />)}

<div>
    Hello perkenalkan saya: {!Author}<br>
    saya: {!Author}<br>
    Hello saya: {!Author}<br>
    Hello perkenalkan: {!Author}<br>
    hehehe: {!Author}<br>
    perkenalkan {!Author}<br>

    <h5>Member</h5>
    <!--{!Member}-->
        {!NamaMember}
        <br/>
        {!Alamat}
        <br/>
        <!--{!Hobi}-->
            Daftar Hobi: {!List}
        <!--{/Hobi}-->
    <!--{/Member}-->
</div>

{sidebar.html}

NAMA MEMBER : {!NamaMember}
<br>

{!js(<script src="template/jquery.min.js" type="text/javascript"></script>)}
```

## About

Crafted with <3 from Bandung, Indonesia.

Copyright 2017 by Didit Velliz

