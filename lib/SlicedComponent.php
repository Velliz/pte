<?php

namespace pte;

class SlicedComponent
{

    /**
     * @var ISlicedComponent
     */
    var $InterfaceSlicer;

    var $StartPosition;
    var $Position;
    var $EndPosition;
    var $Length;
    var $Raw;

    var $Parameter;
    var $Flag;
    var $Child;

    /**
     * @param ISlicedComponent $InterfaceSlicer
     */
    public function __construct(ISlicedComponent $InterfaceSlicer)
    {
        $this->InterfaceSlicer = $InterfaceSlicer;
    }

}