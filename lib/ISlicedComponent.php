<?php
namespace pte;

interface ISlicedComponent
{

    public function SetComponent($FruitSegments);

    public function GetComponent();

    public function GetName();

    public function Output();

    public function Initialize();

}