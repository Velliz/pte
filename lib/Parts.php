<?php

declare(strict_types=1);

namespace pte;

/**
 * Class Parts
 * @package pte
 *
 * Copyright (c) 2017 - Present
 *
 * @author Didit Velliz
 * @link https://github.com/velliz/pte
 * @since Version 0.1.1
 */
abstract class Parts implements CustomRender
{

    /**
     * @var string|false
     * Match tag name in view
     */
    public $tags;

    /**
     * @var string|false
     * Function name called from the template
     */
    public $fnName;

    /**
     * @var mixed
     */
    protected $assets;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var mixed
     */
    protected $paramArray;

    /**
     * @var Pte
     */
    public $pte;

    /**
     * Elements constructor.
     *
     * @param string $tags
     * match tag name in view
     * @param array $data
     * extra data to render in parts
     *
     * @param PteCache|bool|null $cache
     * @param bool|null $master
     * @param bool|null $html
     */
    public function __construct(string $tags, array $data = [], PteCache|bool|null $cache = true, ?bool $master = false, ?bool $html = true)
    {
        $this->pte = new Pte($cache, $master, $html);
        $this->tags = $tags;
        $this->data = $data;
    }

    /**
     * @param mixed $fnName
     * usually have same name as $tags
     * @param mixed $paramArray
     * input parameter from templates
     *
     * this function executed for pre process the custom definition before Parse is executed
     */
    public function RegisterFunction(mixed $fnName, mixed $paramArray)
    {
        $this->fnName = $fnName;
        $this->paramArray = $paramArray;
    }

}
