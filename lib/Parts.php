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

    /**
     * @var Pte
     */
    var $pte;

    /**
     * Elements constructor.
     *
     * @param $tags
     * match tag name in view
     * @param $data
     * extra data to render in parts
     *
     * @param bool $cache
     * @param bool $master
     * @param bool $html
     */
    public function __construct($tags, $data, $cache = true, $master = false, $html = true)
    {
        $this->pte = new Pte($cache, $master, $html);
        $this->tags = $tags;
        $this->data = $data;
    }

    /**
     * @param $fnName
     * usually have same name as $tags
     * @param $paramArray
     * input parameter from templates
     *
     * this function executed for pre process the custom definition before Parse is executed
     */
    public function RegisterFunction($fnName, $paramArray)
    {
        $this->fnName = $fnName;
        $this->paramArray = $paramArray;
    }

}