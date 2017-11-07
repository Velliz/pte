<?php

namespace pte;

use pte\utility\PregOffsetCapture;

class Slicer3
{

    public function Lexer(&$template, &$output, &$position)
    {
        while (preg_match(ISlicer::PATTERN_X, $template, $result, PREG_OFFSET_CAPTURE, $position) > 0) {

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
                $this->Lexer($template, $lex['child'], $position);
            } else {
                unset($lex['child']);
            }
            $output[] = $lex;
        }

        return $output;
    }

}