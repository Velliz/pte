<?php
namespace pte;

use Exception;

class PteException extends Exception
{

    const NOT_FOUND = 10401;
    const READ_ONLY = 10402;
    const REMOTE_ERROR = 10403;

    private $Message = null;

    public function __construct($ErrorCode)
    {
        switch ($ErrorCode) {
            case PteException::NOT_FOUND:
                $this->Message = sprintf("Not found (%s)", PteException::NOT_FOUND);
                break;
            case PteException::READ_ONLY:
                $this->Message = sprintf("File is read-only (%s)", PteException::READ_ONLY);
                break;
            case PteException::REMOTE_ERROR:
                $this->Message = sprintf("Connection failed (%s)", PteException::REMOTE_ERROR);
                break;
            default:
                $this->Message = "Error (UNKNOWN)";
                break;
        }

    }

    function __toString()
    {
        return $this->Message;
    }

}