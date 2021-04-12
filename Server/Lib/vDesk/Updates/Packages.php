<?php
declare(strict_types=1);

namespace vDesk\Updates;


use vDesk\Packages\Package;

final class Packages extends Update {
    
    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\Packages::class;
    
    /**
     * The required version of the Update.
     */
    public const RequiredVersion = "1.0.0";
    
    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Fixed toggling of file dialog for installing Packages.
Description;
    
    /**
     * The files of the Update.
     */
    public const Files = [
        Update::Deploy   => [
            Package::Client => [
                Package::Lib => [
                    "vDesk/Packages/Administration.js"
                ]
            ]
        ],
        Update::Undeploy => [
            Package::Client => [
                Package::Lib => [
                    "vDesk/Packages/Administration.js"
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