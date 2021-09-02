<?php
declare(strict_types=1);

namespace vDesk\Updates;

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
    public const RequiredVersion = "1.0.0";
    
    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Added support for handheld devices.
- Added documentation for Pages MVC-framework.
- Updated documentations and tutorials.
- Removed circular dependency to Homepage-Package.
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
                Package::Lib      => [
                    "vDesk/Documentation/Code.php"
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
                    "Documentation"
                ]
            ]
        ],
        self::Undeploy => [
            Package::Server => [
                Package::Modules      => [
                    "Documentation.php"
                ],
                Package::Lib      => [
                    "vDesk/Documentation/Code.php"
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
                    "Documentation"
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
    }
}