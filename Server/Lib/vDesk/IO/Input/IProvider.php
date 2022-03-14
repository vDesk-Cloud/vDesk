<?php
declare(strict_types=1);

namespace vDesk\IO\Input;

use vDesk\IO\FileInfo;

/**
 * Interface for classes to read data from the current API's input stream.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
interface IProvider {

    /**
     * Pareses the execution relevant parameters either from the CLI or CGI.
     *
     * @param string $Name The name of the parameter to parse.
     *
     * @return string|null The value of the parameter; otherwise, null.
     */
    public function ParseCommand(string $Name): ?string;

    /**
     * Pareses the parameters of the current passed command either from the CLI or CGI.
     *
     * @param string $Name The name of the parameter to parse.
     *
     * @return string|null The value of the parameter; otherwise, null.
     */
    public function ParseParameter(string $Name): ?string;

    /**
     * Fetches a binary file from the current input API.
     *
     * @param string $Name The name of the file parameter to parse.
     *
     * @return \vDesk\IO\FileInfo|null A FileInfo representing the specified file; otherwise, null.
     */
    public function FetchFile(string $Name): ?FileInfo;

}