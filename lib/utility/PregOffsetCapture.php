<?php

namespace pte\utility;

use pte\slicer\ISlicer;

/**
 * Class PregOffsetCapture
 * @package pte\utility
 */
class PregOffsetCapture
{

    /**
     * @var array
     */
    private $RawPregData;

    public function __construct($RawPregData)
    {
        $this->RawPregData = $RawPregData;
    }

    private function PregValidate($key)
    {
        if (!isset($this->RawPregData[$key])) {
            return false;
        }
        if ($this->RawPregData[$key] === -1) {
            return false;
        }
        return true;
    }

    private function PregCaptureValue($key)
    {
        $validate = $this->PregValidate($key);
        if ($validate) {
            return $this->RawPregData[$key][0];
        }
        return false;
    }

    public function PregCaptureStartPosition($key)
    {
        $validate = $this->PregValidate($key);
        if ($validate) {
            return $this->RawPregData[$key][1];
        }
        return false;
    }

    private function PregCaptureFinishPosition($key)
    {
        $validate = $this->PregValidate($key);
        if ($validate) {
            return ($this->RawPregData[$key][1]) + strlen($this->RawPregData[$key][0]);
        }
        return false;
    }

    public function SliceBegin()
    {
        if ($this->PregCaptureValue(ISlicer::BEFORE) !== '') {
            return $this->PregCaptureStartPosition(ISlicer::BEFORE);
        } else {
            return $this->PregCaptureStartPosition(ISlicer::BEGIN);
        }
    }

    public function SliceEnd()
    {
        if ($this->PregCaptureValue(ISlicer::AFTER) !== false) {
            return $this->PregCaptureFinishPosition(ISlicer::AFTER);
        } else {
            return $this->PregCaptureFinishPosition(ISlicer::END);
        }
    }

    public function SliceLength()
    {
        return ($this->SliceEnd() - $this->SliceBegin());
    }

    public function Capture($key)
    {
        $value = $this->PregCaptureValue($key);
        if ($value == '') {
            return false;
        }
        return $value;
    }
}