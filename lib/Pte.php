<?php

declare(strict_types=1);

namespace pte;

use pte\exception\PteException;
use pte\fruits\Fruits;
use pte\slicer\Slicer;
use pte\utility\XmlRpcEncoder;

/**
 * Class Pte
 * @package pte
 *
 * Copyright (c) 2017 - Present
 *
 * @author Didit Velliz
 * @link https://github.com/velliz/pte
 * @since Version 0.1.0
 */
class Pte
{

    #region data types
    /**
     * Value types returned by {@see Pte::GetVarType()}.
     */
    private const TYPE_ARRAYS = 0;
    private const TYPE_STRINGS = 1;
    private const TYPE_BOOLEANS = 2;
    private const TYPE_NULLS = 4;
    private const TYPE_NUMERIC = 5;
    private const TYPE_OBJECTS = 6;
    private const TYPE_UNDEFINED = 7;
    #end region data types

    #region view types
    public const VIEW_HTML = 1;
    public const VIEW_JSON = 2;
    public const VIEW_XML = 3;
    public const VIEW_NULL = 4;
    public const VIEW_JSON_NUMERIC = 5;
    #end region view types

    /**
     * @var float
     */
    private float $ElapsedTime = 0.0;

    /**
     * @var CustomRender|null
     */
    private ?CustomRender $CustomRender = null;

    /**
     * @var PteCache|bool|null
     */
    private PteCache|bool|null $Cache = null;

    /**
     * @var string
     */
    private string $_HtmlData = '';

    /**
     * @var bool
     */
    private bool $_HtmlDataBinary = false;

    /**
     * @var string
     */
    private string $_MasterData = '{CONTENT}';

    /**
     * @var string
     */
    private string $_Output = '';

    /**
     * @var array
     */
    private array $_Value = [];

    /**
     * @var string
     */
    private string $tempJs = '';

    /**
     * @var string
     */
    private string $tempCss = '';

    /**
     * @var Fruits
     */
    private Fruits $fruits;

    /**
     * Pte constructor.
     *
     * @param PteCache|bool|null $cacheDriver
     * @param bool|null $UseMaster
     * @param bool|null $UseBody
     */
    public function __construct(PteCache|bool|null $cacheDriver = null, ?bool $UseMaster = false, ?bool $UseBody = true)
    {
        $this->ElapsedTime = microtime(true);
        $this->Cache = $cacheDriver;

        $this->fruits = new Fruits((bool)$UseMaster, (bool)$UseBody);
    }

    /**
     * @param string $Html
     * @param bool|null $IsBinary
     */
    public function SetHtml(string $Html = '', ?bool $IsBinary = false)
    {
        $this->_HtmlDataBinary = (bool)$IsBinary;
        $this->_HtmlData = $Html;
    }

    /**
     * @param string $Master
     */
    public function SetMaster(string $Master = '{CONTENT}')
    {
        $this->_MasterData = $Master;
    }

    /**
     * @param array $Value
     */
    public function SetValue(array $Value = [])
    {
        $this->_Value = $Value;
    }

    /**
     * @param CustomRender|null $CustomRender
     * @param int|null $Type
     * @param array|null $Segments
     * @return string|null
     * @throws PteException
     */
    public function Output(?CustomRender $CustomRender = null, ?int $Type = Pte::VIEW_HTML, ?array $Segments = [])
    {
        // Every run starts from a clean slate, otherwise the buffers would
        // accumulate the output of the previous call.
        $this->_Output = '';
        $this->tempCss = '';
        $this->tempJs = '';
        $this->ElapsedTime = microtime(true);

        switch ($Type) {
            case Pte::VIEW_HTML:
                // The header has to be sent before anything is written to the
                // output buffer, otherwise PHP complains about headers that
                // were already sent.
                $this->SendHeader('Content-Type: text/html');

                if ($this->fruits->isUseMaster()) {
                    $this->fruits->SetFruitMaster($this->_MasterData);
                }
                if ($this->fruits->isUseBody()) {
                    $this->fruits->SetFruitBody($this->_HtmlData, $this->_HtmlDataBinary);
                }

                foreach ($Segments ?? [] as $val) {
                    $this->fruits->AddFruitSegments($val);
                }

                $Content = $this->BuildContent();

                $this->CustomRender = $CustomRender;

                $output = $this->RenderHtml($Content, $this->_Value);
                break;
            case Pte::VIEW_JSON:
                $output = $this->RenderJson($this->_Value);
                break;
            case Pte::VIEW_JSON_NUMERIC:
                $output = $this->RenderJsonNumeric($this->_Value);
                break;
            case Pte::VIEW_XML:
                $output = $this->RenderXml($this->_Value);
                break;
            case Pte::VIEW_NULL:
                $output = null;
                break;
            default:
                throw new PteException(PteException::OUTPUT_ERROR);
        }

        $this->ElapsedTime = (microtime(true) - $this->ElapsedTime);

        return $output;
    }

    /**
     * Slice the template, using the cache driver when one is available.
     *
     * @return array
     */
    private function BuildContent(): array
    {
        $location = $this->fruits->GetBodyFileLocation();

        if ($this->Cache instanceof PteCache) {
            $Content = $this->Cache->GetTemplate($location);
            if (is_array($Content) && $Content !== []) {
                return $Content;
            }
        }

        $slicer = new Slicer();
        $Content = $slicer->Lexer($this->fruits->GetFruitPack());

        if ($this->Cache instanceof PteCache) {
            $Cached = $this->Cache->SetTemplate($location, $Content);
            if (is_array($Cached)) {
                return $Cached;
            }
        }

        return $Content;
    }

    /**
     * @param $Content
     * @param $Data
     * @return string content is template in array format
     * @throws PteException
     *
     * Content is template in array format
     * Data is returned date from controller
     */
    public function RenderHtml(&$Content, &$Data)
    {
        if (!is_array($Content)) {
            return $this->_Output;
        }

        $this->RenderNodes($Content, $Data);

        return $this->_Output;
    }

    /**
     * Render a set of template nodes and append them to the output buffer.
     *
     * @param array $Content
     * @param mixed $Data
     * @return void
     */
    private function RenderNodes(array $Content, mixed $Data): void
    {
        $Data = is_array($Data) ? $Data : [];

        //scan for assets first
        foreach ($Content as $val) {
            $param = $val['param'] ?? false;
            if ($param !== false) {
                if (($val['key'] ?? null) === 'css') {
                    $this->tempCss .= $param;
                }
                if (($val['key'] ?? null) === 'js') {
                    $this->tempJs .= $param;
                }
            }
        }

        //mix data into templates
        foreach ($Content as $val) {
            $key = $val['key'] ?? false;
            $param = $val['param'] ?? false;
            $hasChild = isset($val['child']) && is_array($val['child']);

            $this->_Output .= $val['text'] ?? '';

            if ($this->CustomRender !== null) {
                $this->CustomRender->RegisterFunction($key, $param);
                $this->_Output .= (string)$this->CustomRender->Parse($param, $this->_HtmlData, $this->_HtmlDataBinary);
            }

            //inject to master
            if ($param !== false && $key === 'part') {
                $slicer = new Slicer();
                if ($param === 'css') {
                    $this->RenderChunk($slicer->Lexer($this->tempCss), $Data);
                }
                if ($param === 'js') {
                    $this->RenderChunk($slicer->Lexer($this->tempJs), $Data);
                }
            }

            // A node without a key is plain text, there is nothing to replace.
            if ($key === false) {
                continue;
            }

            $datum = $Data[$key] ?? null;

            switch ($this->GetVarType($datum)) {
                case self::TYPE_STRINGS:
                    $this->_Output .= (string)$datum;
                    break;
                case self::TYPE_NUMERIC:
                    $this->_Output .= (float)$datum;
                    break;
                case self::TYPE_ARRAYS:
                    foreach ($datum as $v) {
                        if (is_array($v)) {
                            //Numeric Array
                            $this->RenderChunk($val['child'] ?? null, $v);
                        } else {
                            //Associative Arrays
                            $this->RenderChunk($val['child'] ?? null, $datum);
                            break;
                        }
                    }
                    break;
                case self::TYPE_BOOLEANS:
                    if ($datum) {
                        $this->RenderChunk($val['child'] ?? null, $datum);
                    }
                    break;
                case self::TYPE_OBJECTS:
                    if ($datum instanceof Parts) {
                        $datum->RegisterFunction($key, $param);
                        if ($datum->tags === $datum->fnName) {
                            //Append output
                            $this->_Output .= $datum->Parse();
                            //Append property
                            $this->tempCss .= $datum->pte->GetTempCss();
                            $this->tempJs .= $datum->pte->GetTempJs();
                        }
                    }
                    break;
                default:
                    if ($hasChild && $datum !== null) {
                        $this->RenderChunk($val['child'], $datum);
                    }
            }
        }
    }

    /**
     * Render a chunk of nodes and return the part that was added to the buffer.
     *
     * @param mixed $Content
     * @param mixed $Data
     * @return string
     */
    private function RenderChunk(mixed $Content, mixed $Data): string
    {
        if (!is_array($Content)) {
            return '';
        }

        $length = strlen($this->_Output);
        $this->RenderNodes($Content, $Data);

        return substr($this->_Output, $length);
    }

    /**
     * @param $Data
     * @return string
     */
    public function RenderJson(&$Data)
    {
        $this->SendHeader('Content-Type: application/json; charset=utf-8');
        return json_encode($Data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param $Data
     * @return string
     */
    public function RenderJsonNumeric(&$Data)
    {
        $this->SendHeader('Content-Type: application/json; charset=utf-8');
        return json_encode($Data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
    }

    /**
     * Render the data as an XML-RPC document.
     *
     * The encoder is shipped with the library because ext/xmlrpc was removed
     * from the PHP core in PHP 8.0 and only lives on as a PECL extension.
     *
     * @param $Data
     * @return string
     */
    public function RenderXml(&$Data)
    {
        $this->SendHeader('Content-Type: application/xml; charset=utf-8');
        return XmlRpcEncoder::encode($Data);
    }

    /**
     * Send a header, but only when it is still possible to do so.
     *
     * @param string $ContentType
     * @return void
     */
    private function SendHeader(string $ContentType): void
    {
        if (!headers_sent()) {
            header($ContentType);
        }
    }

    /**
     * @param $var
     * @return int
     */
    protected function GetVarType($var)
    {
        return match (true) {
            is_object($var) => self::TYPE_OBJECTS,
            is_array($var) => self::TYPE_ARRAYS,
            is_null($var) => self::TYPE_NULLS,
            is_string($var) => self::TYPE_STRINGS,
            is_bool($var) => self::TYPE_BOOLEANS,
            is_numeric($var) => self::TYPE_NUMERIC,
            default => self::TYPE_UNDEFINED,
        };
    }

    /**
     * @return double
     * Time in seconds
     */
    public function GetElapsedTime()
    {
        return $this->ElapsedTime;
    }

    /**
     * @return string
     */
    public function GetTempJs()
    {
        return $this->tempJs;
    }

    /**
     * @return string
     */
    public function GetTempCss()
    {
        return $this->tempCss;
    }

}
