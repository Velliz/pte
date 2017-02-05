<?php

/**
 * Class template
 */
class template
{

    const KEY_IDENTIFIER = '(\w+)';
    const VALUE_IDENTIFIER = '({!\w+})';
    const FN_IDENTIFIER = '({!\w+\[[\s\S]+\]})';
    const BLOCK_OPEN_IDENTIFIER = '(<!--{!\w+}-->)';
    const BLOCK_CLOSE_IDENTIFIER = '(<!--{!\w+}-->)';
    const CONTENT_IDENTIFIER = '({{CONTENT}})';
    const VIEW_IDENTIFIER = '({\w+}})';
    const STYLE_IDENTIFIER = '({CSS})';
    const SCRIPT_IDENTIFIER = '({JS})';

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
            $Template = $this->Replace(template::CONTENT_IDENTIFIER, $Template, file_get_contents($this->_MasterData));
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