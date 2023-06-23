# PTE - Puko Templating Engine

PTE is a standalone templating engine built for performance and compatibility for standalone use. 
When PTE is in action, it traverses an HTML DOM tree into a PHP array (lexer) and combines the output with data specifications (parser).
On average, it takes 0.004 seconds to render the HTML page with caching turned off, 
and 0.002 seconds with caching turned on, based on the provided example in `index.php`.

### Installation

`composer require velliz/pte` 

The command is used to install the "velliz/pte" package using Composer. It adds the package as a dependency to your project.

Another requirement:

* ext-json
* ext-xmlrpc

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
$pte->SetMaster('template/master.html');
$pte->SetHtml('template/view.html');
```

In the code snippet above, an object of the Pte class is instantiated with three parameters: true for caching the template file, true for using the master file, and true for using the HTML body file.

The master file is set using the SetMaster() method with the file path 'template/master.html', and the HTML file for the view template is set using the SetHtml() method with the file path 'template/view.html'.

You also can create your own parsing rules with custom class like this:

```php
class BaseUrl implements \pte\CustomRender
{

    var $fn;
    var $param;

    var $tempJs = '';
    var $tempCss = '';

    public function Parse($data = null, $template = '', $templateBinary = false)
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

The above code defines the BaseUrl class, which implements the \pte\CustomRender interface. The Parse() method is used to define custom parsing rules for the templating engine. In this example, if the function name is 'url', it returns a URL constructed using the provided parameter. Otherwise, it returns an empty string.

To render tags in the HTML DOM using the BaseUrl custom class, you can use the following code:

```php
{!url(home)}
```

In order to set the data value, you can create an instance of the BaseUrl class and pass it as the first parameter to the SetValue() method. The second parameter should be the data in an array format. Here's an example:

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

To obtain the result, you can use the Output() method, which processes the HTML input using the lexer and parser. Here's an example:
 
```php
$pte->Output($v, Pte::VIEW_HTML);
```

You also have the option to choose Pte::VIEW_JSON as the output format.

---

# Examples

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
    WishList 1
    <hr>
    <!--{!WishList}-->
        <p>Mulai - {!Lingkaran}</p>
        <!--{!Anak}-->
            <div>Ini anak 1 - {!Umur}</div>
            <!--{!Anak2}-->
                <div>Ini anak 2 - {!Umur2}</div>
            <!--{/Anak2}-->
            <div>Ini ulangi anak 1 - {!Umur}</div>
        <!--{/Anak}-->
        <h1>Sekian</h1>
    <!--{/WishList}-->
    <hr>
    <!--{!Wishlist2}-->
    <p>Whistlist 2 {!val}</p>
    <!--{/Wishlist2}-->

    {CONTENT}

    Band 1: {!namaband}
    <!--{!Wishlist2}-->
    <p>Test Whistlist 2 {!val}</p>
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
    Hello: {!Author}<br>
    <h5>Member</h5>
    <!--{!Member}-->
        {!NamaMember}
        <br/>
        {!Alamat}
        <br/>
        <!--{!Hobi}-->
            Hobby: {!List}
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

