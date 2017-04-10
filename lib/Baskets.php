<?php

namespace pte;

use pte\component\tag\Value;

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

    public function Parser($data)
    {
        foreach ($this->Baskets as $val) {
            if ($val instanceof Value) {
                $val->Value = isset($data[$val->Key]) ? $data[$val->Key] : "";
            }
        }
    }

}