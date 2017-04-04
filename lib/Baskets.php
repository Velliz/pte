<?php

namespace pte;

class Baskets
{

    /**
     * @var array
     */
    public $Baskets = array();

    public function __construct()
    {
    }

    public function AddBasket(SlicedComponent $Component)
    {

        array_push($this->Baskets, $Component);

    }

    public function GetBasket()
    {
        return $this->Baskets;
    }

    public function GetBasketComponent()
    {
        return (array)$this->Baskets;
    }

}