<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Packages\Package;

/**
 * MetaInformation Update manifest class.
 *
 * @package vDesk\MetaInformation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class MetaInformation extends Update {

    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\MetaInformation::class;

    /**
     * The required version of the Update.
     */
    public const RequiredVersion = "1.1.1";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Fixed dispatched events.
Description;

    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy   => [
            Package::Server => [
                Package::Lib => [
                    "/vDesk/MetaInformation/DataSet/Created.php",
                    "/vDesk/MetaInformation/DataSet/Updated.php",
                    "/vDesk/MetaInformation/DataSet/Deleted.php",
                    "/vDesk/MetaInformation/Mask/Created.php",
                    "/vDesk/MetaInformation/Mask/Updated.php",
                    "/vDesk/MetaInformation/Mask/Deleted.php"
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