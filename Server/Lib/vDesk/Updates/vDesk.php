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
    public const RequiredVersion = "1.3.0";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Added compatibility to Events-1.2.0.
Description;

    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy   => [
            Package::Server => [
                "vDesk.php"
            ]
        ],
        self::Undeploy => [
            Package::Server => [
                "vDesk.php"
            ]
        ]
    ];

    /** @inheritDoc */
    public static function Install(\Phar $Phar, string $Path): void {
        //Update files.
        self::Undeploy();
        self::Deploy($Phar, $Path);
    }
}