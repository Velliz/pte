<?php

declare(strict_types=1);

namespace pte;

/**
 * Interface PteCache
 * @package pte
 *
 * Copyright (c) 2017 - Present
 *
 * @author Didit Velliz
 * @link https://github.com/velliz/pte
 * @since Version 0.1.1
 */
interface PteCache
{

    /**
     * @param string $templateKeys
     * @return array|false
     */
    public function GetTemplate(string $templateKeys);

    /**
     * @param string $templateKeys
     * @param array $templateData
     * @return array
     */
    public function SetTemplate(string $templateKeys, array $templateData);

}
