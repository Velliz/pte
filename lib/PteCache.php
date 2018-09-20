<?php

namespace pte;

/**
 * Interface PteCache
 * @package pte
 */
interface PteCache
{

    /**
     * @param $templateKeys
     * @return array|false
     */
    public function GetTemplate($templateKeys);

    /**
     * @param $templateKeys
     * @param $templateData
     * @return array
     */
    public function SetTemplate($templateKeys, $templateData);

}