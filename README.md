# PTE - Puko Templating Engine

A lightweight, high-performance PHP templating engine designed for standalone use. PTE traverses HTML DOM trees into PHP arrays (lexer) and combines them with data specifications (parser) to render dynamic pages.

## Features

- **Fast Rendering** - ~0.004s without cache, ~0.002s with caching
- **Master Pages** - Layout inheritance with `{CONTENT}` placeholder
- **Looping** - Iterate over arrays in templates
- **Custom Functions** - Create reusable parsing rules via `CustomRender` interface
- **Partial Views** - Include external template segments
- **Asset Management** - Automatic CSS/JS aggregation
- **Multiple Output Formats** - HTML, JSON, XML support
- **No extension dependencies** - XML output works without `ext-xmlrpc`

## Requirements

- PHP 8.0+ (tested on PHP 8.0 up to and including PHP 8.5)
- Composer
- PHP Extensions: `ext-json` (bundled with PHP since 8.0)

> **Upgrading from 0.2.x?** See [Migrating to PHP 8](#migrating-to-php-8) below.

## Installation

```bash
composer require velliz/pte
```

## Quick Start

```php
<?php
require 'vendor/autoload.php';

use pte\Pte;

$pte = new Pte(true, true, true);
$pte->SetMaster('template/master.html');
$pte->SetHtml('template/view.html');

$pte->SetValue([
    'title' => 'Hello World',
    'users' => [
        ['name' => 'John', 'email' => 'john@example.com'],
        ['name' => 'Jane', 'email' => 'jane@example.com']
    ]
]);

echo $pte->Output();
```

## Template Syntax

### Value Tags
```html
<h1>{!title}</h1>
```

### Loops
```html
<!--{!users}-->
    <p>{!name} - {!email}</p>
<!--{/users}-->
```

### Master Pages
```html
<!-- master.html -->
<!DOCTYPE html>
<html>
<body>
    <header>My Site</header>
    {CONTENT}
</body>
</html>
```

### Custom Functions
```php
class Assets implements \pte\CustomRender
{
    public function Parse(mixed $data = null, string $template = '', bool $templateBinary = false)
    {
        if ($this->fn === 'cdn') {
            return 'https://cdn.example.com/' . $this->param;
        }
        return '';
    }

    public function RegisterFunction(mixed $fnName, mixed $paramArray)
    {
        $this->fn = $fnName;
        $this->param = $paramArray;
    }
}

// Usage in template: {!cdn(js/app.js)}
$pte->Output(new Assets());
```

### Part Objects (Reusable Components)
```php
<?php
use pte\Parts;

class Sidebar extends Parts
{
    public function Parse(mixed $data = null, string $template = '', bool $templateBinary = false)
    {
        $this->pte->SetHtml('template/sidebar.html');
        $this->pte->SetValue($this->data);
        return $this->pte->Output(null, Pte::VIEW_HTML);
    }
}

// Usage: 'sidebar' => new Sidebar('sidebar', ['title' => 'Menu'])
```

### CSS/JS Management
```html
<!-- In view.html -->
{!css(<link href="css/style.css" rel="stylesheet" />)}
{!js(<script src="js/app.js"></script>)}

<!-- In master.html - assets are auto-aggregated -->
{!part(css)}
{!part(js)}
```

### Template Segments
```php
// Load additional templates
$pte->Output($customRender, Pte::VIEW_HTML, [
    'template/footer.html',
    'template/sidebar.html'
]);

<!-- In template -->
{!view(sidebar.html)}
```

## Output Formats

```php
// HTML (default)
$pte->Output($custom, Pte::VIEW_HTML);

// JSON
$pte->Output(null, Pte::VIEW_JSON);

// JSON with numeric strings
$pte->Output(null, Pte::VIEW_JSON_NUMERIC);

// XML (XML-RPC)
$pte->Output(null, Pte::VIEW_XML);
```

## Configuration

| Parameter | Type | Description |
|-----------|------|-------------|
| `$cacheDriver` | `PteCache\|bool\|null` | Cache driver instance or `false` to disable |
| `$UseMaster` | `bool` | Enable master page support |
| `$UseBody` | `bool` | Enable body template parsing |

```php
// No caching, with master, with body
$pte = new Pte(false, true, true);

// With caching, with master, with body
$pte = new Pte(new PteCache(), true, true);

// Just body parsing, no master
$pte = new Pte(false, false, true);
```

## Example Project Structure

```
project/
├── index.php
├── template/
│   ├── master.html
│   ├── view.html
│   ├── sidebar.html
│   └── partial.html
└── vendor/
    └── velliz/pte/
```

## Migrating to PHP 8

Version 0.3.0 makes PTE run on PHP 8.0 up to and including PHP 8.5. The public API
(method names, argument order and public properties) is unchanged, so most
applications only need to update the `composer.json` requirement.

What changed:

- **PHP 8.0+ is required.** The package now declares `"php": ">=8.0"`.
- **`ext-xmlrpc` is no longer needed.** The extension was removed from the PHP
  core in 8.0 and only exists as a PECL extension, which made
  `Pte::RenderXml()` a fatal error. XML output is now produced by the bundled
  `pte\utility\XmlRpcEncoder`.
- **No deprecations left.** The non canonical `(double)` cast was replaced by
  `(float)` (deprecated in PHP 8.5), `var` was replaced by explicit visibility,
  and every file declares `strict_types`.
- **Headers are only sent when possible.** `header()` is now skipped once the
  output has started, so the "Cannot modify header information" warning is gone.

Behaviour fixes that came along with the upgrade:

- `PteException::getMessage()` and `getCode()` now return the error instead of
  an empty string, and `getMessage()` is no longer swallowed by `__toString()`.
- Template and segment contents are inserted literally. `$1`, `\1` and other
  back reference sequences in a template are no longer mangled.
- `Fruits::GetName()` returns the file name (`view`) instead of the path
  (`template/view`).
- `Pte::Output()` can be called more than once on the same instance without the
  output of the previous call leaking into the next one.
- `Pte::GetTempCss()` and `Pte::GetTempJs()` return the collected assets
  instead of the whole rendered page.
- Undefined array key warnings are gone, including for tags without a body.

If you extend the library, note that return types were deliberately **not**
added to public and protected methods, so existing subclasses keep working.
Parameter types were added where it is safe to do so: an override may always
use a wider (or no) parameter type.

## Testing

```bash
php tests/smoke.php
# or
composer test
```

The suite turns every notice, warning and deprecation into a failure, which
makes it a regression guard for the PHP 8.x compatibility of the library.

## Performance

Benchmarks (rendering sample page):
- **Without cache**: ~0.004 seconds
- **With cache**: ~0.002 seconds

## License

MIT License - Copyright 2017 Didit Velliz
