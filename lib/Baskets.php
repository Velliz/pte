<?php

namespace pte;

class Baskets
{

    /**
     * @var array
     */
    protected $Baskets = array();

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

}