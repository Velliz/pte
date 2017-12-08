<?php
namespace pte\fruits;

/**
 * Interface IFruits
 * @package pte\fruits
 *
 * Copyright (c) 2017 - Present
 *
 * @author Didit Velliz
 * @link https://github.com/velliz/pte
 * @since Version 0.1.0
 */
interface IFruits
{

    const CONTENT_IDENTIFIER = '({CONTENT})';

    public function GetFruitPack();

    public function GetLengthOfFruit();

    public function __toString();
}