<?php
namespace pte\Component;

use pte\ISlicedComponent;
use pte\SlicedComponent;

class Constants extends SlicedComponent implements ISlicedComponent
{

    private $Value;

    public function SetValue($FruitSegments)
    {
        $this->Value = $FruitSegments;
    }

    public function GetValue()
    {
        return $this->Value;
    }
}