<?php

namespace pte\exception;

use Exception;

/**
 * Class PteException
 * @package pte\exception
 */
class PteException extends Exception
{

    const NOT_FOUND = 10401;
    const READ_ONLY = 10402;
    const REMOTE_ERROR = 10403;

    /**
     * @var string
     */
    private $Message = '';

    /**
     * PteException constructor.
     * @param string $eCode
     */
    public function __construct($eCode)
    {
        $this->SetError($eCode);
    }

    /**
     * @param $ErrorCode
     */
    public function SetError($ErrorCode)
    {
        switch ($ErrorCode) {
            case PteException::NOT_FOUND:
                $this->Message = sprintf("Template specified not found (%s)", PteException::NOT_FOUND);
                break;
            case PteException::READ_ONLY:
                $this->Message = sprintf("Template file is read-only (%s)", PteException::READ_ONLY);
                break;
            case PteException::REMOTE_ERROR:
                $this->Message = sprintf("Connection failed (%s)", PteException::REMOTE_ERROR);
                break;
            default:
                $this->Message = "Error (UNKNOWN)";
                break;
        }
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->Message;
    }

}