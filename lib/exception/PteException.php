<?php

declare(strict_types=1);

namespace pte\exception;

use Exception;

/**
 * Class PteException
 * @package pte\exception
 *
 * Copyright (c) 2017 - Present
 *
 * @author Didit Velliz
 * @link https://github.com/velliz/pte
 * @since Version 0.1.0
 */
class PteException extends Exception
{

    public const NOT_FOUND = 10401;
    public const READ_ONLY = 10402;
    public const REMOTE_ERROR = 10403;
    public const OUTPUT_ERROR = 10404;

    /**
     * @var string
     */
    private string $Message = '';

    /**
     * PteException constructor.
     *
     * @param int $eCode
     */
    public function __construct(int $eCode)
    {
        $this->SetError($eCode);

        // The message is handed over to the parent constructor as well, so
        // getMessage() and getCode() behave like they do for any other
        // exception instead of returning an empty string.
        parent::__construct($this->Message, $eCode);
    }

    /**
     * @param int $ErrorCode
     */
    public function SetError(int $ErrorCode)
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
            case PteException::OUTPUT_ERROR:
                $this->Message = sprintf("Output error (%s)", PteException::OUTPUT_ERROR);
                break;
            default:
                $this->Message = "Error (UNKNOWN)";
                break;
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->Message;
    }

}
