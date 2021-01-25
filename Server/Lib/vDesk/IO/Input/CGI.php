<?php
declare(strict_types=1);

namespace vDesk\IO\Input;

use vDesk\IO\FileInfo;

/**
 * Common Gateway Interface that provides execution relevant data.
 * Normally invoked through HTTP-Servers.
 *
 * @package vDesk\Connection\Input
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
final class CGI implements IProvider {
    
    /**
     * Parses the execution relevant parameters from the Common Gateway Interface.
     *
     * @param string $Name The name of the parameter to parse.
     *
     * @return string|null The value of the parameter; otherwise, null.
     */
    public function ParseCommand(string $Name): ?string {
        return \filter_input(\INPUT_GET, $Name, 513);
    }
    
    /**
     * Parses the parameters of the current passed command from the Common Gateway Interface.
     *
     * @param string $Name The name of the parameter to parse.
     *
     * @return mixed|null The value of the parameter; otherwise, null.
     */
    public function ParseParameter(string $Name): ?string {
        return \filter_input(\INPUT_POST, $Name);
    }
    
    /**
     * Fetches a binary file from the current input API.
     *
     * @param string $Name The name of the file parameter to fetch.
     *
     * @return \vDesk\IO\FileInfo|null A FileInfo object pointing to an uploaded file; otherwise, null.
     */
    public function FetchFile(string $Name): ?FileInfo {
        return isset($_FILES[$Name]) ? new FileInfo($_FILES[$Name]["tmp_name"]) : null;
    }
}