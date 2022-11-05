<?php
declare(strict_types=1);

namespace vDesk\Pages;

use vDesk\Configuration\Settings;
use vDesk\IO\FileStream;
use vDesk\IO\Output;
use vDesk\IO\Stream\Mode;

/**
 * Class Response
 *
 * @package vDesk\Pages
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Response extends Output {

    /**
     * The HTTP response code of the Response.
     *
     * @var int
     */
    public static int $Code = 200;

    /**
     * Sends data to the current output API.
     *
     * @param mixed $Data The data to send.
     */
    public static function Write(mixed $Data): void {

        $Stream = new FileStream("php://output", Mode::Write | Mode::Binary);
        \header("Content-type: text/html", true, static::$Code);

        if($Data instanceof Page) {
            $Stream->Write($Data->ToDataView());
            return;
        }

        if($Data instanceof \Throwable) {
            if(Settings::$Local["Pages"]["ShowErrors"]) {
                if(\ob_get_level() > 0) {
                    \ob_end_clean();
                }
                $ErrorHandlers = Settings::$Local["ErrorHandlers"];
                $ErrorHandler  = $ErrorHandlers[\get_class($Data)] ?? $ErrorHandlers["Default"];
                static::Write(Modules::Call($ErrorHandler["Module"], $ErrorHandler["Command"], $Data));
                return;
            }

            $Stream->Write($Data->getMessage());
            return;
        }

        $Stream->Write((string)$Data);

    }
}
