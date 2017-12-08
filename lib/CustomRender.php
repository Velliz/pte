<?php

namespace pte;

/**
 * Interface CustomRender
 * @package pte
 *
 * Copyright (c) 2017 - Present
 *
 * @author Didit Velliz
 * @link https://github.com/velliz/pte
 * @since Version 0.1.1
 */
interface CustomRender
{

    /**
     * @param $assets
     */
    public function RegisterAssets($assets);

    /**
     * @param $fnName
     * @param $paramArray
     */
    public function RegisterFunction($fnName, $paramArray);

    /**
     * @return string
     */
    public function Parse();

}