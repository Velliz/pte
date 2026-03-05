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

## Requirements

- PHP 5.6+
- Composer
- PHP Extensions: `ext-json`, `ext-xmlrpc`

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
    public function Parse($data = null, $template = '', $templateBinary = false)
    {
        if ($this->fn === 'cdn') {
            return 'https://cdn.example.com/' . $this->param;
        }
        return '';
    }

    public function RegisterFunction($fnName, $paramArray)
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
    public function Parse($data = null, $template = '', $templateBinary = false)
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

// XML
$pte->Output(null, Pte::VIEW_XML);
```

## Configuration

| Parameter | Type | Description |
|-----------|------|-------------|
| `$cacheDriver` | `PteCache\|bool` | Cache driver instance or `false` to disable |
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

## Performance

Benchmarks (rendering sample page):
- **Without cache**: ~0.004 seconds
- **With cache**: ~0.002 seconds

## License

MIT License - Copyright 2017 Didit Velliz
