<?php

declare(strict_types=1);

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

    /**
     * Regex friendly identifier of the content placeholder.
     */
    const CONTENT_IDENTIFIER = '({CONTENT})';

    /**
     * Literal placeholder that is replaced with the body of the template.
     */
    const CONTENT_PLACEHOLDER = '{CONTENT}';

    public function GetFruitPack();

    public function GetLengthOfFruit();

    public function __toString();
}
