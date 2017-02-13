<?php
namespace pte;

interface IFruits
{
    /*
    const KEY_IDENTIFIER = '(\w+)';
    const VALUE_IDENTIFIER = '({!\w+})';
    const FN_IDENTIFIER = '({!\w+\[[\s\S]+\]})';
    const BLOCK_OPEN_IDENTIFIER = '(<!--{!\w+}-->)';
    const BLOCK_CLOSE_IDENTIFIER = '(<!--{!\w+}-->)';
    */

    const CONTENT_IDENTIFIER = '({{CONTENT}})';
    const VIEW_IDENTIFIER = '({\w+}})';
    const STYLE_IDENTIFIER = '({CSS})';
    const SCRIPT_IDENTIFIER = '({JS})';

    public function GetFruitPack();

    public function GetLengthOfFruit();

    public function __toString();
}