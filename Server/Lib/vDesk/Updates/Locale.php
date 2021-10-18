<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Packages\Package;

/**
 * Locale Update manifest class.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Locale extends Update {
    /**
     * The class name of the Package of the Update.
     */
    public const Package = \vDesk\Packages\Locale::class;
    
    /**
     * The required Package version of the Update.
     */
    public const RequiredVersion = "1.0.1";
    
    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Added compatibility to vDesk-1.0.0.
Description;
    
    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy   => [
            Package::Server => [
                Package::Lib => [
                ]
            ]
        ],
        self::Undeploy => [
            Package::Server => [
                Package::Lib => [
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