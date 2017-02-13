<?php

namespace pte;

use pte\component\Constants;
use pte\component\Dates;
use pte\component\Navigator;
use pte\component\Recrusive;
use pte\component\Tags;
use pte\component\Utility;
use pte\component\Validator;
use pte\component\Value;

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