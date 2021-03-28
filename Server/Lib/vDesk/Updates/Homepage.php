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
class Homepage extends Update {
    
    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\Homepage::class;
    
    /**
     * The required version of the Update.
     */
    public const RequiredVersion = "1.0.0";
    
    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Implemented support for handheld devices.
Description;
    
    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy   => [
            Package::Server => [
                Package::Modules      => [
                    "vDesk.php"
                ],
                IPackage::Pages       => [
                    "vDesk.php",
                    "vDesk"
                ],
                IPackage::Stylesheets => [
                    "vDesk"
                ],
                IPackage::Scripts     => [
                    "vDesk/Index.js"
                ]
            ]
        ],
        self::Undeploy => [
            Package::Server => [
                Package::Modules      => [
                    "vDesk.php"
                ],
                IPackage::Pages       => [
                    "vDesk.php",
                    "vDesk"
                ],
                IPackage::Templates   => [
                    "vDesk.php",
                    "vDesk/Index.php"
                ],
                IPackage::Stylesheets => [
                    "vDesk"
                ],
                IPackage::Scripts     => [
                    "vDesk/Index.js"
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