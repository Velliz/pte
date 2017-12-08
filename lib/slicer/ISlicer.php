<?php
namespace pte\slicer;

/**
 * Interface ISlicer
 * @package pte
 *
 * Copyright (c) 2017 - Present
 *
 * @author Didit Velliz
 * @link https://github.com/velliz/pte
 * @since Version 0.1.0
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

    const A = '<!--';
    const B = '{';
    const C_OPEN = '!';
    const C_CLOSE = '/';
    const D = '!';
    const E = false;
    const F = false;
    const G = '}';
    const H = '-->';
}