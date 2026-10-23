<?php

declare(strict_types=1);

/**
 * PTE - Puko Templating Engine
 * Example Usage File
 *
 * This file demonstrates how to use PTE with various features:
 * - Master pages
 * - Loop iteration
 * - Custom render functions
 * - Part objects (reusable components)
 * - Template segments
 */

// ============================================================================
// AUTOLOADER
// ============================================================================

require_once __DIR__ . '/vendor/autoload.php';

use pte\CustomRender;
use pte\exception\PteException;
use pte\Parts;
use pte\Pte;

// ============================================================================
// CUSTOM RENDER FUNCTIONS
// ============================================================================

/**
 * Base Custom Renderer
 *
 * Implements CustomRender interface to provide custom parsing functions
 * that can be used within templates.
 *
 * Available functions:
 * - {!url(path)}     : Generates full URL with base domain
 * - {!lang(key)}     : Loads translation from JSON file
 */
class Base implements CustomRender
{
    /** @var string|null Tag name being processed */
    public $tags;

    /** @var string|null Function name called in template */
    public $fnName;

    /** @var mixed */
    protected $assets;

    /** @var array|null Data passed to the function */
    protected $data;

    /** @var array|null Parameters passed to the function */
    protected $paramArray;

    /**
     * Parse function handler
     *
     * Called when a custom function tag is encountered in the template.
     *
     * @param mixed  $data              Data passed to the function (from template or SetValue)
     * @param string $template          Current template file path
     * @param bool   $templateBinary     Whether template is binary/string
     * @return string                   Processed output
     */
    public function Parse(mixed $data = null, string $template = '', bool $templateBinary = false)
    {
        // URL builder function: {!url(path)}
        if ($this->fnName === 'url') {
            return 'localhost' . $this->paramArray;
        }

        // Language translation function: {!lang(key)}
        if ($this->fnName === 'lang') {
            if ($data !== null && !$templateBinary) {
                // Try to load translation from JSON file (view.json)
                $resource = str_replace('.html', '.json', $template);
                if (file_exists($resource)) {
                    $content = file_get_contents($resource);
                    $translations = is_string($content) ? json_decode($content, true) : null;
                    if (is_array($translations)) {
                        return $translations[$data] ?? $data;
                    }
                }
            }
        }

        return '';
    }

    /**
     * Register function name and parameters
     *
     * Called by PTE before Parse() to set the function context.
     *
     * @param mixed $fnName     Function name from template (e.g., 'url')
     * @param mixed $paramArray  Parameters passed to the function
     * @return void
     */
    public function RegisterFunction(mixed $fnName, mixed $paramArray)
    {
        $this->fnName = $fnName;
        $this->paramArray = $paramArray;
    }

    /**
     * Register assets (CSS/JS)
     *
     * @param mixed $assets
     * @return void
     */
    public function RegisterAssets(mixed $assets)
    {
        $this->assets = $assets;
    }
}

// ============================================================================
// PART OBJECTS (REUSABLE COMPONENTS)
// ============================================================================

/**
 * Sidebar Component
 *
 * Example of a reusable Part object that can be embedded in templates.
 * Part objects allow you to create self-contained UI components.
 */
class Sidebar extends Parts
{
    /**
     * Parse and render the sidebar component
     *
     * @param mixed  $data              Data passed from parent template
     * @param string $template          Template file path (unused here)
     * @param bool   $templateBinary    Whether template is binary
     * @return string                   Rendered HTML
     */
    public function Parse(mixed $data = null, string $template = '', bool $templateBinary = false)
    {
        // Set the template for this component
        $this->pte->SetHtml('template/sidebar.html');

        // Pass component-specific data
        $this->pte->SetValue($this->data);

        // Render and return the component HTML
        return $this->pte->Output(null, Pte::VIEW_HTML);
    }
}

// ============================================================================
// MAIN TEMPLATE RENDERING
// ============================================================================

/**
 * Initialize PTE
 *
 * Constructor parameters:
 * - $cacheDriver: PteCache instance for caching, or false to disable
 * - $UseMaster:   true to enable master page layout
 * - $UseBody:     true to enable body template parsing
 */
$pte = new Pte(
    $cacheDriver = true,   // Enable caching
    $UseMaster = true,     // Use master page
    $UseBody = true        // Parse body template
);

// Set master template (layout)
$pte->SetMaster('template/master.html');

// Set view template (content)
$pte->SetHtml('template/view.html');

// ============================================================================
// TEMPLATE DATA
// ============================================================================

/**
 * Data to be rendered in the template
 *
 * This data can include:
 * - Simple values (strings, numbers, booleans)
 * - Arrays for loops
 * - Part objects for components
 */
$pte->SetValue([
    // Application info
    'version'       => '1.6.0',
    'appName'       => 'PTE Demo Application',

    // Simple string values
    'FirstCircle'  => 'Welcome to PTE!',
    'Author'       => 'John Developer',
    'AuthorEmail'  => 'john@example.com',

    // Numeric value
    'Age'           => 28,

    // Nested array with multiple loop levels
    'WishList'      => [
        [
            'Item'  => 'Wireless Headphones',
            'Price' => 199.99,
            'Links' => [
                ['Url' => 'https://example.com/1'],
                ['Url' => 'https://example.com/2'],
                ['Url' => 'https://example.com/3'],
            ],
        ],
        [
            'Item'  => 'Mechanical Keyboard',
            'Price' => 149.99,
            'Links' => [
                ['Url' => 'https://example.com/4'],
            ],
        ],
    ],

    // Single associative array (renders once)
    'Wishlist2' => [
        'val' => 'From Controller Data',
    ],

    // Array of objects (team members)
    'TeamMembers' => [
        [
            'Name'     => 'Alice Smith',
            'Role'     => 'Lead Developer',
            'Skills'  => [
                ['Skill' => 'PHP'],
                ['Skill' => 'JavaScript'],
                ['Skill' => 'Docker'],
            ],
        ],
        [
            'Name'     => 'Bob Johnson',
            'Role'     => 'UI/UX Designer',
            'Skills'  => [
                ['Skill' => 'Figma'],
                ['Skill' => 'CSS'],
                ['Skill' => 'Accessibility'],
            ],
        ],
    ],

    // Music genre example
    'MusicGenre' => 'Electronic',

    // Part Object (reusable component)
    'sidebar' => new Sidebar('sidebar', [
        'Title'    => 'Quick Links',
        'Links'    => [
            ['Url' => '/dashboard', 'Label' => 'Dashboard'],
            ['Url' => '/profile',   'Label' => 'Profile'],
            ['Url' => '/settings',  'Label' => 'Settings'],
        ],
    ]),
]);

// ============================================================================
// OUTPUT
// ============================================================================

/**
 * Render the template
 *
 * Parameters:
 * - $customRender: CustomRender instance for custom functions (or null)
 * - $outputType:    Output format (VIEW_HTML, VIEW_JSON, VIEW_XML, etc.)
 * - $segments:      Additional template files to load as segments
 */
$customRenderer = new Base();

$htmlOutput = $pte->Output(
    $customRenderer,              // Custom render functions
    Pte::VIEW_HTML,               // Output as HTML
    ['template/sidebar.html']     // Additional template segments
);

// Display the rendered HTML
echo $htmlOutput;

// Display rendering time (for performance testing)
echo "\n<!-- Rendered in " . $pte->GetElapsedTime() . " seconds -->\n";
