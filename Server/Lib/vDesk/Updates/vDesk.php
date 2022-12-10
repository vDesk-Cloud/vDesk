<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Packages\Package;

/**
 * vDesk Update manifest class.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class vDesk extends Update {
    /**
     * The class name of the Package of the Update.
     */
    public const Package = \vDesk\Packages\vDesk::class;

    /**
     * The required Package version of the Update.
     */
    public const RequiredVersion = "1.3.1";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Updated CSS-classes of calendar and main-menu controls to use client colors.
Description;

    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy   => [
            Package::Client => [
                Package::Design => [
                    "vDesk/MainMenu.css",
                    "vDesk/MainMenu/Item.css",
                    "vDesk/Controls/Calendar/Cell.css"
                ],
                Package::Lib => [
                    "vDesk.php"
                ]
            ],
            Package::Server => [
                Package::Lib => [
                    "vDesk.php"
                ]
            ]
        ]
    ];

    /** @inheritDoc */
    public static function Install(\Phar $Phar, string $Path): void {
        //Update files.
        self::Deploy($Phar, $Path);
    }
}