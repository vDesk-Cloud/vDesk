<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Configuration\Settings;
use vDesk\Packages\Package;
use vDesk\Pages\IPackage;

/**
 * Documentation Update manifest.
 *
 * @package vDesk\Updates
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Documentation extends Update {

    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\Documentation::class;

    /**
     * The required version of the Update.
     */
    public const RequiredVersion = "1.0.2";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Extended database-access documentation.
- Updated development guidelines.
- Refactored Tutorials to Client- and Server-directories.
Description;

    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy   => [
            Package::Server => [
                Package::Modules      => [
                    "Documentation.php"
                ],
                IPackage::Pages       => [
                    "Documentation.php",
                    "Documentation"
                ],
                IPackage::Templates   => [
                    "Documentation.php",
                    "Documentation"
                ],
                IPackage::Stylesheets => [
                    "Documentation/Stylesheet.css",
                    "Documentation/Topics.css"
                ],
                IPackage::Images      => [
                    "Documentation/MachinesArchive.png",
                    "Documentation/MachinesControl.png",
                    "Documentation/Users.png",
                    "Documentation/Groups.png",
                    "Documentation/ACL.png"
                ]
            ]
        ],
        self::Undeploy => [
            Package::Server => [
                Package::Modules      => [
                    "Documentation.php"
                ],
                IPackage::Pages       => [
                    "Documentation.php",
                    "Documentation"
                ],
                IPackage::Templates   => [
                    "Documentation.php",
                    "Documentation"
                ],
                IPackage::Stylesheets => [
                    "Documentation/Stylesheet.css",
                    "Documentation/Tutorials.css"
                ]
            ]
        ]
    ];

    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        self::Undeploy();
        self::Deploy($Phar, $Path);

        //Create routes.
        Settings::$Local["Routes"]["/Documentation/Category/Client/Topic/{Topic}"] = [
            "Module"  => "Documentation",
            "Command" => "ClientPage"
        ];
        Settings::$Local["Routes"]["/Documentation/Category/Client"]               = [
            "Module"  => "Documentation",
            "Command" => "ClientPages"
        ];
        Settings::$Local["Routes"]["/Documentation/Category/Server/Topic/{Topic}"] = [
            "Module"  => "Documentation",
            "Command" => "ServerPage"
        ];
        Settings::$Local["Routes"]["/Documentation/Category/Server"]               = [
            "Module"  => "Documentation",
            "Command" => "ServerPages"
        ];
        Settings::$Local["Routes"]["/Documentation/Topic/{Topic}"]                 = [
            "Module"  => "Documentation",
            "Command" => "Topic"
        ];
        Settings::$Local->Save();
    }
}