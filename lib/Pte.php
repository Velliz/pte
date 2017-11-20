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
     * @return string
     */
    public function RenderHtml(&$Content, &$Data)
    {
        header('Content-Type: text/html');

        foreach ($Content as $key => $val) {
            $this->_Output .= $val['text'];
            $this->_Output .= " ";
            if (isset($Data[$val['key']])) {
                if (!is_array($Data[$val['key']])) {
                    $this->_Output .= $Data[$val['key']];
                } else {
                    //var_dump($Data[$val['key']]);
                }
            }
            if (isset($val['child'])) {
                $this->RenderHtml($val['child'], $Data);
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

}