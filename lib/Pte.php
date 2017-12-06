<?php

namespace pte;

use pte\fruits\Fruits;
use pte\slicer\Slicer;

/**
 * Class Pte
 * @package pte
 */
class Pte
{

    const VERSION = '0.1.0';

    protected $ARRAYS = 0;
    protected $STRINGS = 1;
    protected $BOOLEANS = 2;
    protected $NULLS = 4;
    protected $NUMERIC = 5;
    protected $OBJECTS = 6;
    protected $UNDEFINED = 7;

    const VIEW_HTML = 1;
    const VIEW_JSON = 2;
    const VIEW_XML = 3;
    const VIEW_NULL = 4;

    var $MASTER = true;
    var $HTML = true;

    /**
     * @var string
     */
    public $_HtmlData;

    /**
     * @var string
     */
    public $_MasterData;

    /**
     * @var string
     */
    private $_Output = "";

    /**
     * @var array
     */
    public $_Value = array();

    public function SetHtml($Html = null)
    {
        $this->_HtmlData = $Html;
    }

    public function SetMaster($Master = null)
    {
        $this->_MasterData = $Master;
    }

    public function SetValue($Value = array())
    {
        $this->_Value = $Value;
    }

    public function Output($Type = Pte::VIEW_HTML, $Segments = array())
    {
        $template = new Fruits();
        $template->SetFruitMaster($this->_MasterData);
        $template->SetFruitBody($this->_HtmlData);

        foreach ($Segments as $key => $val) {
            $template->AddFruitSegments($val);
        }

        $slicer = new Slicer();
        $Content = $slicer->Lexer($template->GetFruitPack());

        header('Author: Puko Framework');

        switch ($Type) {
            case Pte::VIEW_HTML:
                echo $this->RenderHtml($Content, $this->_Value);
                break;
            case Pte::VIEW_JSON:
                echo $this->RenderJson($this->_Value);
                break;
            case Pte::VIEW_XML:
                echo $this->RenderXml($this->_Value);
                break;
            case Pte::VIEW_NULL:
                exit();
                break;
        }
    }

    /**
     * @param $Content
     * @param $Data
     *
     * @return string content is template in array format
     *
     * content is template in array format
     * data is returned date from controller
     */
    public function RenderHtml(&$Content, &$Data)
    {
        header('Content-Type: text/html');

        foreach ($Content as $key => $val) {

            $datum = isset($Data[$val['key']]) ? $Data[$val['key']] : null;
            $hasChild = isset($val['child']) ? true : false;

            $this->_Output .= sprintf("%s ", $val['text']);

            if ($this->GetVarType($datum) === $this->STRINGS) {
                $this->_Output .= (string)$Data[$val['key']];
            } else if ($this->GetVarType($datum) === $this->NUMERIC) {
                $this->_Output .= (double)$Data[$val['key']];
            } else if ($this->GetVarType($datum) === $this->ARRAYS) {
                foreach ($datum as $k => $v) {
                    $this->RenderHtml($val['child'], $v);
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
     *
     * @return string
     */
    public function RenderJson(&$Data)
    {
        header('Content-Type: application/json');
        return json_encode($Data);
    }

    /**
     * @param $Data
     *
     * @return string
     */
    public function RenderXml(&$Data)
    {
        header('Content-Type: application/xml');
        return xmlrpc_encode($Data);
    }

    protected function GetVarType($var)
    {
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
        } else {
            return $this->UNDEFINED;
        }
    }
}