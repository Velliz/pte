<?php
namespace pte\slicer;

/**
 * Interface ISlicer
 * @package pte
 */
interface ISlicer
{
    /**
     * Preg Match using regex with named sub patterns
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