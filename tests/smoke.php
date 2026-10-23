<?php

declare(strict_types=1);

/**
 * Smoke tests for PTE.
 *
 * Run with: php tests/smoke.php
 *
 * The suite fails as soon as PHP emits a notice, warning or deprecation, which
 * makes it a regression guard for the PHP 8.x (up to and including 8.5)
 * compatibility of the library.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use pte\CustomRender;
use pte\exception\PteException;
use pte\Parts;
use pte\Pte;

$failures = [];
$assertions = 0;
$diagnostics = [];

// Any diagnostic raised by PHP is turned into a failure.
set_error_handler(static function (int $severity, string $message, string $file = '', int $line = 0): bool {
    global $diagnostics;
    $diagnostics[] = sprintf('%s in %s:%d', $message, $file, $line);
    return true;
});

/**
 * @param mixed $expected
 * @param mixed $actual
 * @param string $message
 */
function assertSame(mixed $expected, mixed $actual, string $message): void
{
    global $failures, $assertions;
    $assertions++;

    if ($expected !== $actual) {
        $failures[] = sprintf(
            "%s\n    expected: %s\n    actual:   %s",
            $message,
            var_export($expected, true),
            var_export($actual, true)
        );
    }
}

/**
 * @param string $needle
 * @param string $haystack
 * @param string $message
 */
function assertContains(string $needle, string $haystack, string $message): void
{
    global $failures, $assertions;
    $assertions++;

    if (!str_contains($haystack, $needle)) {
        $failures[] = sprintf("%s\n    missing: %s", $message, $needle);
    }
}

/**
 * @param string $needle
 * @param string $haystack
 * @param string $message
 */
function assertNotContains(string $needle, string $haystack, string $message): void
{
    global $failures, $assertions;
    $assertions++;

    if (str_contains($haystack, $needle)) {
        $failures[] = sprintf("%s\n    found: %s", $message, $needle);
    }
}

// ============================================================================
// Fixtures
// ============================================================================

$tmp = sys_get_temp_dir() . '/pte-tests-' . getmypid();
@mkdir($tmp, 0777, true);

file_put_contents($tmp . '/view.html', '<h1>{!title}</h1><!--{!items}--><li>{!name}</li><!--{/items}-->');
file_put_contents($tmp . '/master.html', '<html><head>{!part(css)}</head><body>{CONTENT}</body></html>');

/**
 * Custom renderer used by the tests.
 */
class TestRenderer implements CustomRender
{
    public $fnName;
    public $paramArray;

    public function RegisterFunction(mixed $fnName, mixed $paramArray)
    {
        $this->fnName = $fnName;
        $this->paramArray = $paramArray;
    }

    public function Parse(mixed $data = null, string $template = '', bool $templateBinary = false)
    {
        if ($this->fnName === 'upper') {
            return strtoupper((string)$this->paramArray);
        }

        return '';
    }
}

/**
 * Part object used by the tests.
 */
class TestPart extends Parts
{
    public function Parse(mixed $data = null, string $template = '', bool $templateBinary = false)
    {
        $this->pte->SetHtml('<b>{!label}</b>', true);
        $this->pte->SetValue($this->data);

        return $this->pte->Output(null, Pte::VIEW_HTML);
    }
}

/**
 * In memory cache driver used by the tests.
 */
class TestCache implements \pte\PteCache
{
    public array $store = [];
    public int $hits = 0;

    public function GetTemplate(string $templateKeys)
    {
        $this->hits++;

        return $this->store[$templateKeys] ?? false;
    }

    public function SetTemplate(string $templateKeys, array $templateData)
    {
        return $this->store[$templateKeys] = $templateData;
    }
}

// ============================================================================
// Tests
// ============================================================================

// --- Value tags -------------------------------------------------------------
$pte = new Pte(false, false, true);
$pte->SetHtml('<p>{!title}</p>', true);
$pte->SetValue(['title' => 'Hello World']);
assertSame('<p>Hello World</p>', $pte->Output(), 'value tag is replaced');

// --- Numeric values ---------------------------------------------------------
$pte = new Pte(false, false, true);
$pte->SetHtml('<p>{!age}</p>', true);
$pte->SetValue(['age' => 28]);
assertSame('<p>28</p>', $pte->Output(), 'numeric value is rendered');

// --- Loops ------------------------------------------------------------------
$pte = new Pte(false, false, true);
$pte->SetHtml('<ul><!--{!items}--><li>{!name}</li><!--{/items}--></ul>', true);
$pte->SetValue(['items' => [['name' => 'a'], ['name' => 'b'], ['name' => 'c']]]);
assertSame('<ul><li>a</li><li>b</li><li>c</li></ul>', $pte->Output(), 'loop renders every item');

// --- Master page ------------------------------------------------------------
$pte = new Pte(false, true, true);
$pte->SetMaster($tmp . '/master.html');
$pte->SetHtml($tmp . '/view.html');
$pte->SetValue(['title' => 'Master', 'items' => [['name' => 'x']]]);
$output = $pte->Output();
assertContains('<h1>Master</h1>', $output, 'master page keeps the body content');
assertContains('<body>', $output, 'master page is used');
assertSame(1, substr_count($output, '<h1>Master</h1>'), 'master page is rendered once');

// --- Custom render functions ------------------------------------------------
$pte = new Pte(false, false, true);
$pte->SetHtml('<a href="{!upper(/home)}">x</a>', true);
$output = $pte->Output(new TestRenderer());
assertSame('<a href="/HOME">x</a>', $output, 'custom renderer is called');

// --- Part objects -----------------------------------------------------------
$pte = new Pte(false, false, true);
$pte->SetHtml('{!widget}', true);
$pte->SetValue(['widget' => new TestPart('widget', ['label' => 'Part'])]);
assertSame('<b>Part</b>', $pte->Output(), 'part object is rendered');

// --- Segments ---------------------------------------------------------------
file_put_contents($tmp . '/segment.html', '<em>segment</em>');
$pte = new Pte(false, false, true);
$pte->SetHtml('{segment.html}', true);
$output = $pte->Output(null, Pte::VIEW_HTML, [$tmp . '/segment.html']);
assertContains('<em>segment</em>', $output, 'template segment is injected');

// --- Caching ----------------------------------------------------------------
$cache = new TestCache();
$pte = new Pte($cache, false, true);
$pte->SetHtml($tmp . '/view.html');
$pte->SetValue(['title' => 'Cached', 'items' => [['name' => 'x']]]);
$first = $pte->Output();
assertContains('<h1>Cached</h1>', (string)$first, 'cached render produces the output');
assertSame(1, count($cache->store), 'template is stored in the cache');

$pte = new Pte($cache, false, true);
$pte->SetHtml($tmp . '/view.html');
$pte->SetValue(['title' => 'Cached', 'items' => [['name' => 'x']]]);
$second = $pte->Output();
assertSame($first, $second, 'cached render returns the same output');

// --- JSON output ------------------------------------------------------------
$pte = new Pte(false, false, true);
$pte->SetValue(['name' => 'json', 'number' => 5]);
assertSame('{"name":"json","number":5}', $pte->Output(null, Pte::VIEW_JSON), 'json output');

// --- JSON numeric output ----------------------------------------------------
$pte = new Pte(false, false, true);
$pte->SetValue(['number' => '5']);
assertSame('{"number":5}', $pte->Output(null, Pte::VIEW_JSON_NUMERIC), 'json numeric output');

// --- XML output (ext/xmlrpc was removed from PHP core in 8.0) ---------------
$pte = new Pte(false, false, true);
$pte->SetValue(['name' => 'xml', 'count' => 2, 'list' => ['a', 'b']]);
$xml = $pte->Output(null, Pte::VIEW_XML);
assertContains('<?xml version="1.0" encoding="UTF-8"?>', (string)$xml, 'xml declaration is present');
assertContains('<string>xml</string>', (string)$xml, 'xml string is encoded');
assertContains('<int>2</int>', (string)$xml, 'xml integer is encoded');
assertContains('<array><data>', (string)$xml, 'xml list is encoded as an array');

// --- NULL output ------------------------------------------------------------
$pte = new Pte(false, false, true);
assertSame(null, $pte->Output(null, Pte::VIEW_NULL), 'null output');

// --- Unknown output type raises an exception --------------------------------
$pte = new Pte(false, false, true);
try {
    $pte->Output(null, 999);
    $failures[] = 'unknown output type should raise a PteException';
    $assertions++;
} catch (PteException $e) {
    $assertions++;
    assertSame(PteException::OUTPUT_ERROR, $e->getCode(), 'exception carries the error code');
    assertSame(true, $e->getMessage() !== '', 'exception carries a message');
}

// --- A null output type is reported as an output error ------------------------
$pte = new Pte(false, false, true);
try {
    $pte->Output(null, null);
    $failures[] = 'a null output type should raise a PteException';
    $assertions++;
} catch (PteException $e) {
    $assertions++;
    assertSame(PteException::OUTPUT_ERROR, $e->getCode(), 'a null output type is an output error');
}

// --- A null segment list is accepted ------------------------------------------
$pte = new Pte(false, false, true);
$pte->SetHtml('<p>{!title}</p>', true);
$pte->SetValue(['title' => 'Segments']);
assertSame('<p>Segments</p>', $pte->Output(null, Pte::VIEW_HTML, null), 'a null segment list is accepted');

// --- Repeated output does not accumulate ------------------------------------
$pte = new Pte(false, false, true);
$pte->SetHtml('<p>{!title}</p>', true);
$pte->SetValue(['title' => 'Twice']);
assertSame('<p>Twice</p>', $pte->Output(), 'first render');
assertSame('<p>Twice</p>', $pte->Output(), 'second render does not accumulate');

// --- Literal dollar signs and backslashes in the template -------------------
$pte = new Pte(false, false, true);
$pte->SetHtml('<p>$1 and \1 and {!title}</p>', true);
$pte->SetValue(['title' => 'ok']);
assertSame('<p>$1 and \1 and ok</p>', $pte->Output(), 'template content is not treated as a replacement pattern');

// --- Missing template --------------------------------------------------------
$pte = new Pte(false, true, true);
$pte->SetMaster($tmp . '/does-not-exist.html');
$pte->SetHtml($tmp . '/view.html');
$pte->SetValue(['title' => 'Master', 'items' => [['name' => 'x']]]);
ob_start();
$output = $pte->Output();
$reported = (string)ob_get_clean();
assertContains('Template specified not found', $reported, 'missing master reports the error');
assertContains('<h1>Master</h1>', (string)$output, 'missing master falls back to the body');

// ============================================================================
// Result
// ============================================================================

restore_error_handler();

foreach (glob($tmp . '/*') ?: [] as $file) {
    @unlink($file);
}
@rmdir($tmp);

if ($diagnostics !== []) {
    $failures[] = "PHP raised diagnostics:\n    " . implode("\n    ", $diagnostics);
}

echo sprintf("Ran %d assertions", $assertions), PHP_EOL;

if ($failures === []) {
    echo "OK - all assertions passed", PHP_EOL;
    exit(0);
}

echo sprintf("FAILED (%d)", count($failures)), PHP_EOL;
foreach ($failures as $failure) {
    echo ' - ', $failure, PHP_EOL;
}
exit(1);
