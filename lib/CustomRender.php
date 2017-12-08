<?php

namespace pte;

interface CustomRender
{

    /**
     * @param $tags
     */
    public function SetTags($tags);

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