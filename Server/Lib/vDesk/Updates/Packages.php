<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Packages\Package;

/**
 * Packages Update manifest class.
 *
 * @package vDesk\Packages
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Packages extends Update {

    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\Packages::class;

    /**
     * The required version of the Update.
     */
    public const RequiredVersion = "1.1.0";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Removed potential access on deleted directory.
- Fixed automatic bundling of package manifest files.
Description;

    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy => [
            Package::Server => [
                Package::Lib     => [
                    "vDesk/Packages/Package.php"
                ],
                Package::Modules => [
                    "Packages.php"
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