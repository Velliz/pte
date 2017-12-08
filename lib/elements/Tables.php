<?php

namespace pte\elements;

/**
 * Class Tables
 * @package pte\elements
 */
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
     * @return string
     */
    public function Parse()
    {
        return $this->paramArray;
    }
}