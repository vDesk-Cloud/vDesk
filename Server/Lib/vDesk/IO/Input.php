<?php
declare(strict_types=1);

namespace vDesk\IO;

use vDesk\Environment\API;
use vDesk\IO\Input\CGI;
use vDesk\IO\Input\CLI;
use vDesk\Modules\Command;
use vDesk\Modules\ICommand;

/**
 * Class that parses commands from teh current API's input stream.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Input {

    /**
     * Reads the input from the current API and parses a Command from the read data.
     *
     * @return \vDesk\Modules\ICommand The parsed Command.
     * @todo Remove dependency.
     */
    public static function Read(): ICommand {
        switch(API::$Current) {
            case API::CGI:
                return Command::Parse(new CGI());
            case API::CLI:
                return Command::Parse(new CLI());
            case API::Socket:
                //return Command::Parse(new CGI());
            default:
                $Class = "\\vDesk\\IO\\Input\\" . API::$Current;
                return Command::Parse(new $Class());
        }
    }

    /**
     * Parses the execution relevant parameters from the current input API.
     *
     * @param string $Name The name of the parameter to parse.
     *
     * @return mixed|null The value of the parameter.
     */
    public function ParseCommand(string $Name): ?string {
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
    public function FetchFile(string $Name): ?FileInfo {
        if(\PHP_SAPI === "cli"){
            return ($Parameter = self::ParseParameter($Name)) !== false ? new FileInfo($Parameter[$Name]) : null;
        }
        return isset($_FILES[$Name]) ? new FileInfo($_FILES[$Name]["tmp_name"]) : null;
    }
}