<?php

namespace pte\component;

use pte\ISlicedComponent;
use pte\SlicedComponent;

class View extends SlicedComponent implements ISlicedComponent
{

    private $Component;

    public function SetComponent($FruitSegments)
    {
        $this->Component = $FruitSegments;
    }

    public function GetComponent()
    {
        return $this->Component;
    }

    public function Output()
    {
        return $this->Component;
    }

    public function Initialize()
    {

    }

    public function GetName()
    {
        // TODO: Implement GetName() method.
    }
}