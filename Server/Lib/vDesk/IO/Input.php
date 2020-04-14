<?php
declare(strict_types=1);

namespace vDesk\IO;

use vDesk\Environment\API;
use vDesk\IO\Input\CGI;
use vDesk\IO\Input\CLI;
use vDesk\Modules\Command;
use vDesk\Modules\ICommand;

/**
 * Class Input
 *
 * @package vDesk\IO
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Input {
    
    /**
     * Reads the input from the current API and parses a Command from the read data.
     *
     * @return \vDesk\Modules\ICommand The parsed Command.
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
}