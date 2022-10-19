<?php
declare(strict_types=1);

namespace vDesk\IO;

use vDesk\Environment\API;
use vDesk\IO\Output\CGI;
use vDesk\IO\Output\CLI;

/**
 * Provides abstract access to the current output stream.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Output {

    /**
     * Writes data to the output-stream.
     *
     * @param mixed $Data The data to write.
     */
    public static function Write(mixed $Data): void {
        switch(API::$Current) {
            case API::CGI:
                CGI::Write($Data);
                break;
            case API::CLI:
                CLI::Write($Data);
                break;
            case API::Socket:
                //return Command::Parse(new CGI());
                break;
            default:
                ("\\vDesk\\IO\\Output\\" . API::$Current)::Write($Data);
        }
    }

}