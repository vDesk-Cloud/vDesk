<?php
declare(strict_types=1);

namespace vDesk\Packages;

/**
 * Crash Package manifest.
 *
 * @package vDesk\Crash
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Crash extends Package {

    /**
     * The name of the Package.
     */
    public const Name = "Crash";

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
    public const Description = "Package providing an unit test framework.";

    /**
     * The dependencies of the Package.
     */
    public const Dependencies = ["Pages" => "1.1.1"];

    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Server => [
            self::Modules => [
                "Crash.php"
            ],
            self::Lib     => [
                "vDesk/Crash"
            ]
        ]
    ];

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