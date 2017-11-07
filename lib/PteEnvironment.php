<?php

namespace pte;

class PteEnvironment
{

    const PTE_VERSION = '0.1.0-alpha';

    const MASTER_TRUE = true;
    const MASTER_FALSE = false;
    const HTML_TRUE = true;
    const HTML_FALSE = false;

    /**
     * @var string
     */
    public $_HtmlData;

    /**
     * @var string
     */
    public $_MasterData;

    public $_Value;

    public $_View = array();

    public $_Token = array();

    /**
     * template constructor.
     */
    public function __construct()
    {

    }

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

    public function SetView($Value = null)
    {
        array_push($this->_View, $Value);
    }

    public function Output($IsCached = false, $IsHtml = true, $IsMaster = false)
    {
        $HtmlFile = $MasterFile = $Template = null;
        if ($IsHtml) {
            $Template = file_get_contents($this->_HtmlData);
        }
        if ($IsMaster) {
            $Template = $this->Replace(IFruits::CONTENT_IDENTIFIER, $Template, file_get_contents($this->_MasterData));
        }
        foreach ($this->_View as $Segment) {
            $Template = $this->Replace('({{'.$Segment.'}})', file_get_contents($Segment), $Template);
        }

        echo $Template;

    }

    private function Replace($regex, $replacement, $source)
    {
        return preg_replace($regex, $replacement, $source);
    }
}