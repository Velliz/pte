<?php

namespace pte;

/**
 * Class Parts
 * @package pte
 *
 * Copyright (c) 2017 - Present
 *
 * @author Didit Velliz
 * @link https://github.com/velliz/pte
 * @since Version 0.1.1
 */
abstract class Parts implements CustomRender
{

    var $tags;
    var $fnName;

    protected $assets;
    protected $data;
    protected $paramArray;
    protected $pte;

    /**
     * Elements constructor.
     * @param $tags
     * @param $data
     */
    public function __construct($tags, $data)
    {
        $this->pte = new Pte(true, false, true);
        $this->tags = $tags;
        $this->data = $data;
    }

    /**
     * @param $fnName
     * @param $paramArray
     */
    public function RegisterFunction($fnName, $paramArray)
    {
        $this->fnName = $fnName;
        $this->paramArray = $paramArray;
    }

    /**
     * @param $assets
     */
    public function RegisterAssets($assets)
    {
        $this->assets = $assets;
    }

}