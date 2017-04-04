<?php
namespace pte\component\tag;

use pte\component\Tag;
use pte\ISlicedComponent;

class Navigator extends Tag implements ISlicedComponent
{

    public function SetComponent($FruitSegments)
    {

    }

    public function GetComponent()
    {
        return $this->Before . $this->Begin . $this->Flag . $this->Inverse . $this->Key . $this->Parameter . $this->End . $this->After;
    }

    public function Output()
    {

    }

    public function Initialize()
    {

    }
}