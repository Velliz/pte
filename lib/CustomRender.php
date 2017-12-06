<?php

namespace pte;

interface CustomRender
{

    /**
     * @param $fnName
     * @param $paramArray
     */
    public function Register($fnName, $paramArray);

    /**
     * @return string
     */
    public function Parse();

}