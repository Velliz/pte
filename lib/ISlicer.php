<?php
namespace pte;

interface ISlicer
{
    /**
     * preg_match with Named subpatterns
     */
    const PATTERN_X = '@(?<A><!--)*(?<B>{)(?<C>[!|/])(?<D>[!])?(?<E>[a-zA-Z0-9\._-]+)(?:\((?<F>[\S\s]*?)\))?(?<G>})(?<H>-->)*@';
    const PATTERN = '@(?<BO><!--)*(?<O>{)(?<Flag>[!|/])(?<Inverse>[!])?(?<Key>[a-zA-Z0-9\._-]+)(?:\((?<Parameter>[\S\s]*?)\))?(?<E>})(?<BE>-->)*@';

    const BEFORE = 'A';
    const BEGIN = 'B';
    const FLAG = 'C';
    const INVERSE = 'D';
    const KEY = 'E';
    const PARAMETER = 'F';
    const END = 'G';
    const AFTER = 'H';

}