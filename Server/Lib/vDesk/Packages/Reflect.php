<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\Pages\IPackage;

/**
 * Reflect Package manifest class.
 *
 * @package Reflect
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Reflect extends Package implements IPackage {

    /**
     * The name of the Package.
     */
    public const Name = "Reflect";

    /**
     * The version of the Package.
     */
    public const Version = "0.0.1";

    /**
     * The name of the Package.
     */
    public const Vendor = "Kerry <DevelopmentHero@gmail.com>";

    /**
     * The name of the Package.
     */
    public const Description = "Package that provides a reflection based class API generator.";

    /**
     * The dependencies of the Package.
     */
    public const Dependencies = ["Pages" => "1.0.1"];

    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Server => [
            self::Modules     => [
                "Reflect.php"
            ],
            self::Pages       => [
                "Reflect.php",
                "Reflect"
            ],
            self::Templates   => [
                "Reflect.php",
                "Reflect"
            ],
            self::Stylesheets => [
                "Reflect"
            ],
            self::Scripts     => [
                "Reflect"
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