<?php

namespace pte;

use pte\exception\PteException;
use pte\fruits\Fruits;
use pte\slicer\Slicer;

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
    private $ARRAYS = 0;
    private $STRINGS = 1;
    private $BOOLEANS = 2;
    private $NULLS = 4;
    private $NUMERIC = 5;
    private $OBJECTS = 6;
    private $UNDEFINED = 7;
    #end region data types

    #region view types
    const VIEW_HTML = 1;
    const VIEW_JSON = 2;
    const VIEW_XML = 3;
    const VIEW_NULL = 4;
    #end region data types

    /**
     * @var float
     */
    private $ElapsedTime = 0.0;

    /**
     * @var CustomRender
     */
    private $CustomRender;

    /**
     * @var bool
     */
    private $Cache = false;

    /**
     * @var string
     */
    private $_HtmlData = '';

    /**
     * @var string
     */
    private $_MasterData = '{CONTENT}';

    /**
     * @var string
     */
    private $_Output = '';

    /**
     * @var array
     */
    private $_Value = array();

    /**
     * @var string
     */
    private $tempJs = '';

    /**
     * @var string
     */
    private $tempCss = '';

    /**
     * @var Fruits
     */
    private $fruits;

    /**
     * Pte constructor.
     *
     * @param bool $cache
     * @param bool $UseMaster
     * @param bool $UseBody
     */
    public function __construct($cache = false, $UseMaster = false, $UseBody = true)
    {
        $this->ElapsedTime = microtime(true);
        $this->Cache = $cache;

        $this->fruits = new Fruits($UseMaster, $UseBody);
    }

    /**
     * @param string $Html
     */
    public function SetHtml($Html = '')
    {
        $this->_HtmlData = $Html;
    }

    /**
     * @param string $Master
     */
    public function SetMaster($Master = '{CONTENT}')
    {
        $this->_MasterData = $Master;
    }

    /**
     * @param array $Value
     */
    public function SetValue($Value = array())
    {
        $this->_Value = $Value;
    }

    /**
     * @param CustomRender $CustomRender
     * @param int $Type
     * @param array $Segments
     * @return string
     * @throws PteException
     */
    public function Output($CustomRender = null, $Type = Pte::VIEW_HTML, $Segments = array())
    {
        switch ($Type) {
            case Pte::VIEW_HTML:
                if ($this->fruits->isUseMaster()) {
                    $this->fruits->SetFruitMaster($this->_MasterData);
                }
                if ($this->fruits->isUseBody()) {
                    $this->fruits->SetFruitBody($this->_HtmlData);
                }

                foreach ($Segments as $val) {
                    $this->fruits->AddFruitSegments($val);
                }

                if ($this->Cache) {
                    if (!file_exists($this->fruits->GetName())) {
                        $slicer = new Slicer();
                        $Content = $slicer->Lexer($this->fruits->GetFruitPack());
                        file_put_contents($this->fruits->GetName(), json_encode($Content));
                    } else {
                        $Content = json_decode(file_get_contents($this->fruits->GetName()), true);
                    }
                } else {
                    $slicer = new Slicer();
                    $Content = $slicer->Lexer($this->fruits->GetFruitPack());
                }

                $this->CustomRender = $CustomRender;

                $output = $this->RenderHtml($Content, $this->_Value);
                break;
            case Pte::VIEW_JSON:
                $output = $this->RenderJson($this->_Value);
                break;
            case Pte::VIEW_XML:
                $output = $this->RenderXml($this->_Value);
                break;
            case Pte::VIEW_NULL:
                $output = null;
                break;
            default:
                throw new PteException(PteException::OUTPUT_ERROR);
                break;
        }

        $this->ElapsedTime = (microtime(true) - $this->ElapsedTime);

        return $output;
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
        header('Content-Type: text/html');

        foreach ($Content as $key => $val) {

            $datum = isset($Data[$val['key']]) ? $Data[$val['key']] : null;
            $hasChild = isset($val['child']) ? true : false;

            $this->_Output .= $val['text'];

            if ($this->CustomRender !== null) {
                $this->CustomRender->RegisterFunction($val['key'], $val['param']);
                $this->_Output .= $this->CustomRender->Parse();
            }

            if ($val['param'] !== false) {
                if ($val['key'] === 'css') {
                    $this->tempCss .= $val['param'];
                }
                if ($val['key'] === 'js') {
                    $this->tempJs .= $val['param'];
                }

                if ($val['key'] === 'part') {
                    if ($val['param'] === 'css') {
                        $this->_Output .= $this->tempCss;
                    }
                    if ($val['param'] === 'js') {
                        $this->_Output .= $this->tempJs;
                    }
                }
            }

            if ($this->GetVarType($datum) === $this->STRINGS) {
                $this->_Output .= (string)$Data[$val['key']];
            } else if ($this->GetVarType($datum) === $this->NUMERIC) {
                $this->_Output .= (double)$Data[$val['key']];
            } else if ($this->GetVarType($datum) === $this->ARRAYS) {
                foreach ($datum as $k => $v) {
                    if (is_array($v)) {
                        //Numeric Array
                        $this->RenderHtml($val['child'], $v);
                    } else {
                        //Associative Arrays
                        $this->RenderHtml($val['child'], $datum);
                        break;
                    }
                }
            } else if ($this->GetVarType($datum) === $this->BOOLEANS) {
                if ($datum) {
                    $this->RenderHtml($val['child'], $datum);
                }
            } else if ($this->GetVarType($datum) === $this->OBJECTS) {
                if ($datum instanceof Parts) {
                    $datum->RegisterFunction($val['key'], $val['param']);
                    if ($datum->tags === $datum->fnName) {
                        //Append output
                        $this->_Output .= $datum->Parse();
                        //Append property
                        $this->tempCss .= $datum->pte->GetTempCss();
                        $this->tempJs .= $datum->pte->GetTempJs();
                    }
                } else {
                    throw new PteException(PteException::OUTPUT_ERROR);
                }
            } else {
                if ($hasChild && $datum !== null) {
                    $this->RenderHtml($val['child'], $datum);
                }
            }
        }
        return $this->_Output;
    }

    /**
     * @param $Data
     * @return string
     */
    public function RenderJson(&$Data)
    {
        header('Content-Type: application/json');
        return json_encode($Data);
    }

    /**
     * @param $Data
     * @return string
     */
    public function RenderXml(&$Data)
    {
        header('Content-Type: application/xml');
        return xmlrpc_encode($Data);
    }

    /**
     * @param $var
     * @return int
     */
    protected function GetVarType($var)
    {
        if (is_object($var)) {
            return $this->OBJECTS;
        }
        if (is_array($var)) {
            return $this->ARRAYS;
        }
        if (is_null($var)) {
            return $this->NULLS;
        }
        if (is_string($var)) {
            return $this->STRINGS;
        }
        if (is_bool($var)) {
            return $this->BOOLEANS;
        }
        if (is_numeric($var)) {
            return $this->NUMERIC;
        }
        return $this->UNDEFINED;
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