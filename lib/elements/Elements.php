<?php

namespace pte\elements;

use pte\CustomRender;

abstract class Elements implements CustomRender
{

    protected $tags;
    protected $assets;
    protected $data;

    protected $fnName;
    protected $paramArray;

    /**
     * Elements constructor.
     * @param $tags
     * @param $data
     */
    public function __construct($tags, $data)
    {
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