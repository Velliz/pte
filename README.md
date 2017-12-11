# PTE - Puko Templating Engine

PTE is a standalone templating engine build for performance and compatible for standalone use. 
PTE in action traverses a HTML DOM tree into a PHP array (lexer) and then combining output with data specs (parser).

### Infograph

Seconds average to render the html page with provided example in **index.php**

* 0.004 s with caching off.
* 0.002 s with caching on.

### Installation

Try it with composer command:

```
composer require velliz/pte
```

Or you can download it directly, run composer install after it. 

### Tags Available

| Tags | Description |
| --- | --- |
| `{!x}` | Value or Part Objects tags |
| `<!--{!x}-->` | **open** loop tags |
| `<!--{/x}-->` | **close** loop tags |
| `{!fn()}` | **function** tags with no parameters |
| `{!fn(x)}` | **function** tags with one parameters |
| `{!fn(x,y,z)}` | **function** tags with multiple parameters |
| `{CONTENT}` | **CONTENT** tags only works on master file |
| `{!css(<link href="" rel="stylesheet" type="text/css" />)}` | **CSS** tags |
| `{!js(<script src="" type="text/javascript"></script>)}` | **JavaScript** tags |
| `{!part(css)}` | move **CSS** tags location to this tag |
| `{!part(js)}` | move **JavaScript** tags location to this tag |
| `{x.html}` | segment file tags |

### Usage sample

Instance the object:

```php
$pte = new Pte(false, true, true);
```

First param true for cache the template file. Second param true for using master file.
Last param true for using html body file.

Html file splitted into two piece.
The first one is called master for base html template (you can see in **master.html** bellow) 
and the other called html template (you can see in **view.html** bellow)

```php
$pte->SetMaster('template/master.html');
$pte->SetHtml('template/view.html');
```

You also can create your own parsing rules with custom class like this:

```php
class BaseUrl implements \pte\CustomRender
{

    var $fn;
    var $param;

    var $tempJs = '';
    var $tempCss = '';

    public function Parse()
    {
        if ($this->fn === 'url') {
            return 'http://localhost/' . $this->param;
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

And using it to render tags in Html DOM like this:

```php
{!url(home)}
```

You can set the data value like this:

```php
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
    'namaband' => 'K-POP',
    'Umur' => 23,
    'Author' => 'Didit Velliz',
    'Member' => array(
        array(
            'NamaMember' => 'Universitas X',
            'Alamat' => 'Bandung, Indonesia',
            'Hobi' => array(
                array('List' => 'Makan'),
                array('List' => 'Traveling'),
                array('List' => 'Tidur'),
            ),
        )
    ),
    'NamaMember' => 'Laptop Gaming',
));
```

You will notice that **SetValue** method requires 2 input param, first the custom renderer class, 
and second is the data in array.

Then you can get the result via **Output** method like this:
 
```php
$pte->Output($v, Pte::VIEW_HTML);
```

The output method is the part when the lexer and parser process the Html input.
You also have a choice for choosing *Pte::VIEW_JSON* for output.
Happy coding :)

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

