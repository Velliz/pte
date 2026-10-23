<?php

declare(strict_types=1);

namespace pte\utility;

use pte\slicer\ISlicer;

/**
 * Class PregOffsetCapture
 * @package pte\utility
 *
 * Copyright (c) 2017 - Present
 *
 * @author Didit Velliz
 * @link https://github.com/velliz/pte
 * @since Version 0.1.0
 */
class PregOffsetCapture
{

    /**
     * @var array
     */
    private array $RawPregData;

    /**
     * @param array $RawPregData
     */
    public function __construct(array $RawPregData)
    {
        $this->RawPregData = $RawPregData;
    }

    /**
     * @param string $key
     * @return bool
     */
    private function PregValidate(string $key)
    {
        if (!isset($this->RawPregData[$key])) {
            return false;
        }

        if (!is_array($this->RawPregData[$key])) {
            return false;
        }

        if ($this->RawPregData[$key][1] === -1) {
            return false;
        }

        return true;
    }

    /**
     * @param string $key
     * @return string|false
     */
    private function PregCaptureValue(string $key)
    {
        $validate = $this->PregValidate($key);
        if ($validate) {
            return $this->RawPregData[$key][0];
        }

        return false;
    }

    /**
     * @param string $key
     * @return int|false
     */
    public function PregCaptureStartPosition(string $key)
    {
        $validate = $this->PregValidate($key);
        if ($validate) {
            return $this->RawPregData[$key][1];
        }

        return false;
    }

    /**
     * @param string $key
     * @return int|false
     */
    private function PregCaptureFinishPosition(string $key)
    {
        $validate = $this->PregValidate($key);
        if ($validate) {
            return ($this->RawPregData[$key][1]) + strlen($this->RawPregData[$key][0]);
        }

        return false;
    }

    /**
     * @return int|false
     */
    public function SliceBegin()
    {
        // The optional groups are reported as an empty match with the offset
        // -1 when they did not take part in the match, so only a real,
        // non empty match can be used to locate the slice.
        $before = $this->PregCaptureValue(ISlicer::BEFORE);
        if (is_string($before) && $before !== '') {
            return $this->PregCaptureStartPosition(ISlicer::BEFORE);
        }

        return $this->PregCaptureStartPosition(ISlicer::BEGIN);
    }

    /**
     * @return int|false
     */
    public function SliceEnd()
    {
        $after = $this->PregCaptureValue(ISlicer::AFTER);
        if (is_string($after) && $after !== '') {
            return $this->PregCaptureFinishPosition(ISlicer::AFTER);
        }

        return $this->PregCaptureFinishPosition(ISlicer::END);
    }

    /**
     * @return int|false
     */
    public function SliceLength()
    {
        $begin = $this->SliceBegin();
        $end = $this->SliceEnd();
        if ($begin === false || $end === false) {
            return false;
        }

        return ($end - $begin);
    }

    /**
     * @param string $key
     * @return string|false
     */
    public function Capture(string $key)
    {
        $value = $this->PregCaptureValue($key);
        if ($value === '') {
            return false;
        }

        return $value;
    }

}
