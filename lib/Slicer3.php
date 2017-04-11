<?php

namespace pte;

use pte\utility\PregOffsetCapture;

class Slicer3
{

    public function Lexer($template, &$output, &$position = 0)
    {
        while (preg_match(ISlicer::PATTERN_X, $template, $Result, PREG_OFFSET_CAPTURE, $position) > 0) {

            $capture = new PregOffsetCapture($Result);

            $lex['start'] = trim(substr($template, $position, $capture->SliceBegin() - $position));

            $lex['slice_start'] = $capture->SliceBegin();
            $lex['slice_end'] = $capture->SliceEnd();
            $lex['slice_length'] = $capture->SliceLength();

//            $lex['before'] = $capture->Capture(ISlicer::BEFORE);
//            $lex['begin'] = $capture->Capture(ISlicer::BEGIN);
//            $lex['flag'] = $capture->Capture(ISlicer::FLAG);
//            $lex['inverse'] = $capture->Capture(ISlicer::INVERSE);
            $lex['key'] = $capture->Capture(ISlicer::KEY);
//            $lex['parameter'] = $capture->Capture(ISlicer::PARAMETER);
//            $lex['end'] = $capture->Capture(ISlicer::END);
//            $lex['after'] = $capture->Capture(ISlicer::AFTER);

            if ($capture->Capture(ISlicer::FLAG) == '/') {
                $position = $capture->SliceEnd();
                break;
            }

            if ($capture->Capture(ISlicer::BEFORE) != false && $capture->Capture(ISlicer::AFTER) != false) {
                $position = $capture->SliceEnd();
                $child = array();
                $parser = $this->Lexer($template, $child, $position);
                $lex['child'] = $parser;
                $output[] = $lex;
            } else {
                $position = $capture->SliceEnd();
                $output[] = $lex;
            }
        }

        return $output;
    }

}