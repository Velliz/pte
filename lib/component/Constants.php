<?php
namespace pte\component;

use pte\ISlicedComponent;
use pte\SlicedComponent;

class Constants extends SlicedComponent implements ISlicedComponent
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

    }

    public function Initialize()
    {

    }
}