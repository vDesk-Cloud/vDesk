<?php
declare(strict_types=1);

namespace vDesk\IO\Input;

use vDesk\IO\FileInfo;

/**
 * Interface providing functionality for reading data from a command line interface.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
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
     * @param string $Name The path to the file of the file parameter to fetch.
     *
     * @return \vDesk\IO\FileInfo|null
     */
    public function FetchFile(string $Name): ?FileInfo {
        return ($Parameter = \getopt("", ["{$Name}:"])) !== false ? new FileInfo($Parameter[$Name]) : null;
    }
}