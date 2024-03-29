<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Packages\Package;

/**
 * Updates Update manifest class.
 *
 * @package vDesk\Updates
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Updates extends Update {

    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\Updates::class;

    /**
     * The required version of the Update.
     */
    public const RequiredVersion = "1.1.0";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Removed potential access on deleted directory.
- Fixed automatic bundling of update manifest files.
Description;

    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy => [
            Package::Server => [
                Package::Lib     => [
                    "vDesk/Updates/Update.php"
                ],
                Package::Modules => [
                    "Updates.php"
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