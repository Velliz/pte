<?php

namespace pte\component;

use pte\ISlicedComponent;
use pte\SlicedComponent;

class ViewSegment extends SlicedComponent implements ISlicedComponent
{

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
        return $this;
    }

    public function Initialize()
    {

    }
}