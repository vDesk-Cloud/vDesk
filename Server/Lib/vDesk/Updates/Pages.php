<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Packages\Package;

/**
 * Pages Update manifest class.
 *
 * @package vDesk\Pages
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Pages extends Update {

    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\Pages::class;

    /**
     * The required version of the Update.
     */
    public const RequiredVersion = "1.1.1";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Added compatibility to vDesk-1.2.0.
Description;

    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy   => [
            Package::Server => [
                Package::Lib => [
                    "Pages.php",
                    "vDesk/Pages/Request.php",
                    "vDesk/Pages/Request/Parameters.php",
                    "vDesk/Pages/Response.php",
                    "vDesk/Pages/Cached/Page.php"
                ]
            ]
        ],
        self::Undeploy => [
            Package::Server => [
                Package::Lib => [
                    "Pages.php",
                    "vDesk/Pages/Request.php",
                    "vDesk/Pages/Request/Parameters.php",
                    "vDesk/Pages/Response.php",
                    "vDesk/Pages/Cached/Page.php"
                ]
            ]
        ]
    ];

    /** @inheritDoc */
    public static function Install(\Phar $Phar, string $Path): void {
        self::Uneploy();
        self::Deploy($Phar, $Path);
    }
}