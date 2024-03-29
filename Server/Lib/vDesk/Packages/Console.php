<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\Locale\IPackage;

/**
 * Console Package manifest class.
 *
 * @package vDesk\Packages
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Console extends Package implements IPackage {

    /**
     * The name of the Package.
     */
    public const Name = "Console";

    /**
     * The version of the Package.
     */
    public const Version = "1.1.0";

    /**
     * The vendor of the Package.
     */
    public const Vendor = "Kerry <DevelopmentHero@gmail.com>";

    /**
     * The description of the Package.
     */
    public const Description = "Package that enables a client side console.";

    /**
     * The dependencies of the Package.
     */
    public const Dependencies = ["Security" => "1.1.0"];

    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Client => [
            self::Design  => [
                "vDesk/Console.css"
            ],
            self::Modules => [
                "Console.js"
            ],
            self::Lib     => [
                "vDesk/Console.js",
                "vDesk/Console"
            ]
        ]
    ];

    /**
     * The translations of the Package.
     */
    public const Locale = [
        "DE" => [
            "Permissions" => [
                "UseConsole" => "Legt fest ob Mitglieder der Gruppe die Konsole benutzen können"
            ]
        ],
        "EN" => [
            "Permissions" => [
                "UseConsole" => "Determines whether members of the group are allowed to use the console"
            ]
        ],
        "NL" => [
            "Permissions" => [
                "UseConsole" => "Bepaalt of leden van de groep de console mogen gebruiken"
            ]
        ]
    ];

    /** @inheritDoc */
    public static function Install(\Phar $Phar, string $Path): void {

        //Create permissions.
        /** @var \Modules\Security $Security */
        $Security = \vDesk\Modules::Security();
        $Security::CreatePermission("UseConsole", false);

        //Extract files.
        self::Deploy($Phar, $Path);

    }

    /** @inheritDoc */
    public static function Uninstall(string $Path): void {

        //Delete permissions.
        /** @var \Modules\Security $Security */
        $Security = \vDesk\Modules::Security();
        $Security::DeletePermission("UseConsole");

        //Delete files.
        self::Undeploy();

    }
}