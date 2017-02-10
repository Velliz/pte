<?php
namespace pte;

interface ISlicer
{
    /**
     * preg_match with Named subpatterns
     */
    const PATTERN = '@(?<Before><!--)*(?<Begin>{)(?<Flag>[!|/])(?<Inverse>[!])?(?<Key>[a-zA-Z0-9\._-]+)(?:\((?<Parameter>[\S\s]*?)\))?(?<End>})(?<After>-->)*@';

    const BEFORE = 'Before';
    const BEGIN = 'Begin';
    const FLAG = 'Flag';
    const INVERSE = 'Inverse';
    const KEY = 'Key';
    const PARAMETER = 'Parameter';
    const END = 'End';
    const AFTER = 'After';

}