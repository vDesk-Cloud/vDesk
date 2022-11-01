<?php
declare(strict_types=1);

namespace vDesk\IO;

/**
 * Class that parses values from the current API's input stream.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Input {

    /**
     * Parses the execution relevant parameters from the current input API.
     *
     * @param string $Name The name of the parameter to parse.
     *
     * @return mixed|null The value of the parameter.
     */
    public static function ParseCommand(string $Name): ?string {
        if(\PHP_SAPI === "cli"){
            return (($Parameter = \getopt("{$Name[0]}:")) !== false) ? $Parameter[$Name[0]] ?? self::ParseParameter($Name) : null;
        }
        return \filter_input(\INPUT_GET, $Name, 513);
    }

    /**
     * Parses the parameters of the current passed command from the current input API.
     *
     * @param string $Name The name of the parameter to parse.
     *
     * @return mixed|null The value of the parameter; otherwise, null.
     */
    public static function ParseParameter(string $Name): ?string {
        if(\PHP_SAPI === "cli"){
            return (($Parameter = \getopt("", ["{$Name}:"])) !== false) ? $Parameter[$Name] : null;
        }
        return \filter_input(\INPUT_POST, $Name);
    }

    /**
     * Fetches a binary file from the current input API.
     *
     * @param string $Name The name of the file parameter to fetch.
     *
     * @return \vDesk\IO\FileInfo|null A FileInfo object pointing to an uploaded file; otherwise, null.
     */
    public static function FetchFile(string $Name): ?FileInfo {
        if(\PHP_SAPI === "cli"){
            return ($Parameter = self::ParseParameter($Name)) !== false ? new FileInfo($Parameter[$Name]) : null;
        }
        return isset($_FILES[$Name]) ? new FileInfo($_FILES[$Name]["tmp_name"]) : null;
    }
}