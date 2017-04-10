<?php
namespace pte;

interface IFruits
{

    const CONTENT_IDENTIFIER = '({{CONTENT}})';
    const VIEW_IDENTIFIER = '({\w+}})';
    const STYLE_IDENTIFIER = '({CSS})';
    const SCRIPT_IDENTIFIER = '({JS})';

    public function GetFruitPack();

    public function GetLengthOfFruit();

    public function __toString();
}