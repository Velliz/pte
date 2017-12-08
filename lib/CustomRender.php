<?php

namespace pte;

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