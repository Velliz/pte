<?php

declare(strict_types=1);

namespace pte;

/**
 * Interface CustomRender
 * @package pte
 *
 * Copyright (c) 2017 - Present
 *
 * @author Didit Velliz
 * @link https://github.com/velliz/pte
 * @since Version 0.1.1
 */
interface CustomRender
{

    /**
     * Prepare the custom definition before Parse() is executed.
     *
     * @param mixed $fnName
     * Usually the same name as the tag used in the template.
     * @param mixed $paramArray
     * Input parameter from the template.
     */
    public function RegisterFunction(mixed $fnName, mixed $paramArray);

    /**
     * @param mixed $data
     * @param string $template
     * @param bool $templateBinary
     * @return string
     */
    public function Parse(mixed $data = null, string $template = '', bool $templateBinary = false);

}
