<?php

namespace pte\elements;

class Tables extends Elements
{

    /**
     * Tables constructor.
     * @param $tags
     * @param $data
     */
    public function __construct($tags, $data)
    {
        parent::__construct($tags, $data);
    }

    /**
     * @param $tags
     */
    public function SetTags($tags)
    {

    }

    /**
     * @param $assets
     */
    public function RegisterAssets($assets)
    {
        // TODO: Implement RegisterAssets() method.
    }

    /**
     * @param $fnName
     * @param $paramArray
     */
    public function RegisterFunction($fnName, $paramArray)
    {
    }

    /**
     * @return string
     */
    public function Parse()
    {
        return '<h1>TABLES</h1>';
    }
}