<?php

declare(strict_types=1);

namespace pte\slicer;

use pte\utility\PregOffsetCapture;

/**
 * Class Slicer
 * @package pte
 *
 * Slicing a fruits into piece of fruit segments
 *
 * Copyright (c) 2017 - Present
 *
 * @author Didit Velliz
 * @link https://github.com/velliz/pte
 * @since Version 0.1.0
 */
class Slicer
{

    /**
     * @param string $template
     * @param array $output
     * @param int $position
     * @return array
     *
     * Process the last of the converted html element
     */
    public function Lexer(string $template, array $output = [], int $position = 0): array
    {
        $output = $this->PregCapture($template, $output, $position);

        $length = strlen($template);
        $lex = array();
        $lex['text'] = substr($template, $position, $length);

        $lex['start'] = $position;
        $lex['end'] = $length;
        $lex['length'] = ($length - $position);
        $lex['flag'] = false;
        $lex['inverse'] = false;
        $lex['key'] = false;
        $lex['param'] = false;

        $output[] = $lex;

        return $output;
    }

    /**
     * @param string $template
     * @param array $output
     * @param int $position
     * @return array
     *
     * Converting html file into AST in PHP array
     */
    private function PregCapture(string &$template, array &$output, int &$position): array
    {
        while (preg_match(ISlicer::PATTERN, $template, $result, PREG_OFFSET_CAPTURE, $position) > 0) {
            if ($result[0][1] === -1) {
                break;
            }

            $capture = new PregOffsetCapture($result);

            $lex = array();
            $lex['text'] = substr($template, $position, $capture->SliceBegin() - $position);

            $lex['start'] = $capture->SliceBegin();
            $lex['end'] = $capture->SliceEnd();
            $lex['length'] = $capture->SliceLength();
            $lex['flag'] = $capture->Capture(ISlicer::FLAG);
            $lex['inverse'] = $capture->Capture(ISlicer::INVERSE);
            $lex['key'] = $capture->Capture(ISlicer::KEY);
            $lex['param'] = $capture->Capture(ISlicer::PARAMETER);

            $position = $lex['end'];

            if ($capture->Capture(ISlicer::FLAG) === ISlicer::C_CLOSE) {
                $output[] = $lex;
                break;
            }

            $before = $capture->Capture(ISlicer::BEFORE);
            $after = $capture->Capture(ISlicer::AFTER);
            $a = $before === ISlicer::A || $before === '<!--';
            $b = $after === ISlicer::H || $after === '-->';
            if ($a && $b) {
                $lex['child'] = [];
                $this->PregCapture($template, $lex['child'], $position);
            } else {
                unset($lex['child']);
            }

            $output[] = $lex;
        }

        return $output;
    }

}
