<?php

namespace pte\Utility;

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

    public function PregCaptureValue($key)
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

    public function PregCaptureFinishPosition($key)
    {
        $validate = $this->PregValidate($key);
        if ($validate) {
            return ($this->RawPregData[$key][1]) + strlen($this->RawPregData[$key][0]);
        }
        return false;
    }
}