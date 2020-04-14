<?php
declare(strict_types=1);

namespace vDesk\IO\Input;

use vDesk\IO\FileInfo;

/**
 * Command Line Interface that provides execution relevant data.
 *
 * @package vDesk\Connection\Input
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
final class CLI implements IProvider {

    /**
     * Parses the execution relevant parameters from the Command Line Interface.
     *
     * @param string $Name The name of the parameter to parse.
     *
     * @return mixed|null The value of the parameter.
     */
    public function ParseCommand(string $Name): ?string {
        return (($Parameter = \getopt("{$Name[0]}:")) !== false) ? $Parameter[$Name[0]] ?? $this->ParseParameter($Name) : null;
    }

    /**
     * Parses the parameters of the current passed command from the Command Line Interface.
     *
     * @param string $Name The name of the parameter to parse.
     *
     * @return mixed|null The value of the parameter.
     */
    public function ParseParameter(string $Name): ?string {
        return (($Parameter = \getopt("", ["{$Name}:"])) !== false) ? $Parameter[$Name] : null;
    }

    /**
     * Fetches a binary file from the current input API.
     *
     * @param string $Path The path to the file of the file parameter to fetch.
     *
     * @return \vDesk\IO\FileInfo|null
     */
    public function FetchFile(string $Path): ?FileInfo {
        return ($Parameter = \getopt("", ["{$Path}:"])) !== false ? new FileInfo($Parameter[$Path]) : null;
    }
}