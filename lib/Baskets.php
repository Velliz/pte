<?php

namespace pte;

use pte\Component\Constants;
use pte\Component\Dates;
use pte\Component\Navigator;
use pte\Component\Recrusive;
use pte\Component\Tags;
use pte\Component\Utility;
use pte\Component\Validator;
use pte\Component\Value;

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

        var_dump($Component);

        if ($Component instanceof Constants) {

        } else if ($Component instanceof Dates) {

        } else if ($Component instanceof Navigator) {

        } else if ($Component instanceof Recrusive) {

        } else if ($Component instanceof Tags) {

        } else if ($Component instanceof Utility) {

        } else if ($Component instanceof Validator) {

        } else if ($Component instanceof Value) {

        } else {

        }
    }

    public function GetBasket()
    {
        return $this->Baskets;
    }

}