<?php
namespace pte;

interface ISlicedComponent
{

    public function SetComponent($FruitSegments);

    public function GetComponent();

    public function Output();

    public function Initialize();
}