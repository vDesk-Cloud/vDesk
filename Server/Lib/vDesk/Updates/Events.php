<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Packages\Package;

/**
 * Events Update manifest class.
 *
 * @package vDesk\Events
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Events extends Update {

    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\Events::class;

    /**
     * The required Package version of the Update.
     */
    public const RequiredVersion = "1.2.0";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Added compatibility to Archive-1.1.2.
Description;

    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy => [
            Package::Server => [
                Package::Modules => [
                    "Events.php"
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