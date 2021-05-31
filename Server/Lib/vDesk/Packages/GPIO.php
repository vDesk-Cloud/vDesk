<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\Environment\OS;
use vDesk\Utils\Log;

/**
 * GPIO Package manifest class.
 *
 * @package vDesk\GPIO
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class GPIO extends Package {

    /**
     * The name of the Package.
     */
    public const Name = "GPIO";

    /**
     * The version of the Package.
     */
    public const Version = "1.0.0";

    /**
     * The name of the Package.
     */
    public const Vendor = "Kerry <DevelopmentHero@gmail.com>";

    /**
     * The name of the Package.
     */
    public const Description = "Package providing a stream based GPIO interface for Raspberry Pis.";

    /**
     * The dependencies of the Package.
     */
    public const Dependencies = ["vDesk" => "1.0.0"];

    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Server => [
            self::Lib => [
                "vDesk/GPIO/Pin.php"
            ]
        ]
    ];

    /**
     * @inheritDoc
     */
    public static function PreInstall(\Phar $Phar, string $Path): void {
        if(OS::Current !== OS::Linux){
            Log::Warn(__METHOD__, "Package only supports Linux systems.");
        }
    }

    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        self::Deploy($Phar, $Path);
    }

    /**
     * @inheritDoc
     */
    public static function Uninstall(string $Path): void {
        self::Undeploy();
    }
}