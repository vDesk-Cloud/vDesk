<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Packages\Package;
use vDesk\Pages\IPackage;

/**
 * Homepage Update manifest.
 *
 * @package Homepage
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Homepage extends Update {

    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\Homepage::class;

    /**
     * The required version of the Update.
     */
    public const RequiredVersion = "1.0.1";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Updated roadmap.
- Fixed typo on Packages page.
- Fixed fullscreen display of images.
Description;

    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy   => [
            Package::Server => [
                IPackage::Templates   => [
                    "vDesk/GetvDesk.php",
                    "vDesk/Packages.php",
                    "vDesk/Roadmap.php"
                ],
                IPackage::Stylesheets => [
                    "vDesk/Stylesheet.css"
                ],
                IPackage::Images      => [
                    "vDesk/Installation.png"
                ]
            ]
        ],
        self::Undeploy => [
            Package::Server => [
                IPackage::Templates   => [
                    "vDesk/GetvDesk.php",
                    "vDesk/Packages.php",
                    "vDesk/Roadmap.php"
                ],
                IPackage::Stylesheets => [
                    "vDesk/Stylesheet.css"
                ],
                IPackage::Images      => [
                    "vDesk/Installation.png"
                ]
            ]
        ]
    ];

    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {

        //Update files.
        self::Undeploy();
        self::Deploy($Phar, $Path);

    }
}