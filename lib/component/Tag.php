<?php

namespace pte\component;

use pte\component\tag\Blocks;
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
     * @var array
     */
    public $Child = array();
    public $Pointer = null;

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
        $this->Child[0] = $Child;
    }

    public function AppendChild(SlicedComponent $Child)
    {
        $this->Child[sizeof($this->Child)] = $Child;
    }

    public function AppendBlock(SlicedComponent $Child)
    {
        $this->Child[sizeof($this->Child) - 1] = $Child;
    }

    public function GetName()
    {
        // TODO: Implement GetName() method.
    }
}