<?php
namespace pte;

interface ISlicer
{
    /**
     * preg_match with Named subpatterns
     */
    const PATTERN = '@(?<A><!--)*(?<B>{)(?<C>[!|/])(?<D>[!])?(?<E>[a-zA-Z0-9\._-]+)(?:\((?<F>[\S\s]*?)\))?(?<G>})(?<H>-->)*@';

    const BEFORE = 'A';
    const BEGIN = 'B';
    const FLAG = 'C';
    const INVERSE = 'D';
    const KEY = 'E';
    const PARAMETER = 'F';
    const END = 'G';
    const AFTER = 'H';

}