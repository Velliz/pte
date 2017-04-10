<?php

namespace pte\component;

use pte\ISlicedComponent;
use pte\SlicedComponent;

class Tag extends SlicedComponent implements ISlicedComponent
{

    public $Before;
    public $Begin;
    public $Flag;
    public $Inverse;
    public $Key;
    public $Parameter;
    public $End;
    public $After;

    /**
     * @var SlicedComponent
     */
    public $Child;

    public function SetComponent($FruitSegments)
    {

    }

    public function GetComponent()
    {
        return $this->Before . $this->Begin . $this->Flag . $this->Inverse . $this->Key . $this->Parameter . $this->End . $this->After;
    }

    public function Output()
    {
        return $this->Value;
    }

    public function Initialize()
    {

    }

    public function SetChild(SlicedComponent $Child)
    {
        $this->Child = $Child;
    }

    public function GetName()
    {
        // TODO: Implement GetName() method.
    }
}