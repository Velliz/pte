<?php
namespace pte\fruits;

/**
 * Interface IFruits
 * @package pte\fruits
 */
interface IFruits
{

    const CONTENT_IDENTIFIER = '({{CONTENT}})';

    public function GetFruitPack();

    public function GetLengthOfFruit();

    public function __toString();
}