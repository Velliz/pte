<?php

namespace pte\elements;

use pte\CustomRender;

abstract class Elements implements CustomRender
{

    /**
     * Elements constructor.
     * @param $tags
     * @param $data
     */
    public function __construct($tags, $data)
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