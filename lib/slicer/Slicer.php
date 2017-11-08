<?php

namespace pte\slicer;

use pte\utility\PregOffsetCapture;

/**
 * Class Slicer
 * @package pte
 *
 * slicing a fruits into piece of fruit segments
 */
class Slicer
{

    /**
     * @param $template
     * @param array $output
     * @param int $position
     * @return array
     *
     * process the last of the converted html element
     */
    public function Lexer($template, $output = array(), $position = 0)
    {
        $output = $this->PregCapture($template, $output, $position);

        $length = strlen($template);
        $lex = array();
        $lex['text'] = trim(substr($template, $position, $length));
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
     * @param $template
     * @param $output
     * @param $position
     * @return array
     *
     * converting html file into AST in PHP array
     */
    private function PregCapture(&$template, &$output, &$position)
    {
        while (preg_match(ISlicer::PATTERN, $template, $result, PREG_OFFSET_CAPTURE, $position) > 0) {

            $capture = new PregOffsetCapture($result);

            $lex = array();
            $lex['text'] = trim(substr($template, $position, $capture->SliceBegin() - $position));

            $lex['start'] = $capture->SliceBegin();
            $lex['end'] = $capture->SliceEnd();
            $lex['length'] = $capture->SliceLength();
            $lex['flag'] = $capture->Capture(ISlicer::FLAG);
            $lex['inverse'] = $capture->Capture(ISlicer::INVERSE);
            $lex['key'] = $capture->Capture(ISlicer::KEY);
            $lex['param'] = $capture->Capture(ISlicer::PARAMETER);

            $position = $lex['end'];

            if ($capture->Capture(ISlicer::FLAG) === '/') {
                $output[] = $lex;
                break;
            }
            if ($capture->Capture(ISlicer::BEFORE) === '<!--' && $capture->Capture(ISlicer::AFTER) === '-->') {
                $this->PregCapture($template, $lex['child'], $position);
            } else {
                unset($lex['child']);
            }
            $output[] = $lex;
        }

        return $output;
    }

}