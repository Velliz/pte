<?php
namespace pte\component\tag;

use pte\component\Tag;
use pte\ISlicedComponent;

class Value extends Tag implements ISlicedComponent
{

    public function SetComponent($FruitSegments)
    {

    }

    public function GetComponent()
    {
        return $this->Parameter;
    }

    public function Output()
    {

    }

    public function Initialize()
    {

    }
}