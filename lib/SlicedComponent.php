<?php

namespace pte;

/**
 * Class SlicedComponent
 * Class for hold sliced fruit segment and anatomy
 *
 * @author Didit Velliz
 * @mail diditvelliz@gmail.com
 * @lisence MIT
 * @package pte
 * @copyright 2017
 */
abstract class SlicedComponent
{

    /**
     * @var int
     */
    public $StartPosition;
    public $EndPosition;
    public $Length;
    public $Value;

    /**
     * Position of the slice loops
     * @var int
     */
    public $Position;

    public function __construct($StartPosition, $EndPosition, $Length, $Position)
    {
        $this->StartPosition = $StartPosition;
        $this->EndPosition = $EndPosition;
        $this->Length = $Length;

        $this->Position = $Position;
    }

}