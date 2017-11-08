<?php
namespace pte\fruits;

/**
 * Interface IFruits
 * @package pte\fruits
 */
interface IFruits
{

    const CONTENT_IDENTIFIER = '({{CONTENT}})';
    const STYLE_IDENTIFIER = '({CSS})';
    const SCRIPT_IDENTIFIER = '({JS})';

    public function GetFruitPack();

    public function GetLengthOfFruit();

    public function __toString();
}