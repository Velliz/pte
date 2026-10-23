<?php

declare(strict_types=1);

namespace pte\utility;

/**
 * Class XmlRpcEncoder
 * @package pte\utility
 *
 * Minimal, dependency free XML-RPC encoder.
 *
 * The `ext-xmlrpc` extension was removed from the PHP core in PHP 8.0 and is
 * only available as a PECL extension, which makes `xmlrpc_encode()` unusable
 * on a default PHP 8.x (and therefore PHP 8.5) installation. This class
 * produces the very same document without requiring any extension.
 *
 * Copyright (c) 2017 - Present
 *
 * @author Didit Velliz
 * @link https://github.com/velliz/pte
 * @since Version 0.3.0
 */
final class XmlRpcEncoder
{
    /**
     * Encode a value into an XML-RPC document.
     *
     * @param mixed $value
     * @return string
     */
    public static function encode(mixed $value): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<params>' . "\n"
            . '<param>' . "\n"
            . '<value>' . "\n"
            . self::encodeValue($value) . "\n"
            . '</value>' . "\n"
            . '</param>' . "\n"
            . '</params>' . "\n";
    }

    /**
     * Encode a single value into its XML-RPC representation.
     *
     * @param mixed $value
     * @return string
     */
    public static function encodeValue(mixed $value): string
    {
        if ($value === null) {
            return '<nil/>';
        }

        if (is_bool($value)) {
            return '<boolean>' . ($value ? '1' : '0') . '</boolean>';
        }

        if (is_int($value)) {
            return '<int>' . $value . '</int>';
        }

        if (is_float($value)) {
            return '<double>' . self::encodeDouble($value) . '</double>';
        }

        if (is_array($value)) {
            return self::isList($value)
                ? self::encodeArray($value)
                : self::encodeStruct($value);
        }

        if (is_object($value)) {
            if ($value instanceof \Stringable || method_exists($value, '__toString')) {
                return self::encodeString((string)$value);
            }

            return self::encodeStruct(get_object_vars($value));
        }

        if (is_resource($value)) {
            return '<base64>' . base64_encode((string)$value) . '</base64>';
        }

        return self::encodeString((string)$value);
    }

    /**
     * @param array $value
     * @return string
     */
    private static function encodeArray(array $value): string
    {
        $data = '';
        foreach ($value as $item) {
            $data .= '<value>' . self::encodeValue($item) . '</value>';
        }

        return '<array><data>' . $data . '</data></array>';
    }

    /**
     * @param array $value
     * @return string
     */
    private static function encodeStruct(array $value): string
    {
        $members = '';
        foreach ($value as $name => $item) {
            $members .= '<member>'
                . '<name>' . self::escape((string)$name) . '</name>'
                . '<value>' . self::encodeValue($item) . '</value>'
                . '</member>';
        }

        return '<struct>' . $members . '</struct>';
    }

    /**
     * @param string $value
     * @return string
     */
    private static function encodeString(string $value): string
    {
        return '<string>' . self::escape($value) . '</string>';
    }

    /**
     * Format a float the way XML-RPC expects it.
     *
     * @param float $value
     * @return string
     */
    private static function encodeDouble(float $value): string
    {
        if (is_nan($value) || is_infinite($value)) {
            // XML-RPC has no representation for NAN/INF.
            return '0';
        }

        return var_export($value, true);
    }

    /**
     * Escape a value for use inside an XML text node.
     *
     * @param string $value
     * @return string
     */
    private static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    /**
     * Determine whether the given array is a list (sequential integer keys).
     *
     * `array_is_list()` is only available since PHP 8.1, so a compatible
     * implementation is used instead.
     *
     * @param array $value
     * @return bool
     */
    private static function isList(array $value): bool
    {
        $expected = 0;
        foreach ($value as $key => $unused) {
            if ($key !== $expected++) {
                return false;
            }
        }

        return true;
    }
}
