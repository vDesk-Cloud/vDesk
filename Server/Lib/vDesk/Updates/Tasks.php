<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Modules;
use vDesk\Packages\Package;

/**
 * Tasks Update manifest class.
 *
 * @package vDesk\Tasks
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Tasks extends Update {
    
    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\Tasks::class;
    
    /**
     * The required version of the Update.
     */
    public const RequiredVersion = "1.0.0";
    
    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Added support for scheduling intervals in microseconds.
- Implemented machine like flow control callbacks for Tasks.
Description;
    
    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Undeploy   => [
            Package::Server => [
                Package::Lib     => [
                    "vDesk/Tasks/Tasks.php"
                ]
            ]
        ],
        self::Deploy   => [
            Package::Server => [
                Package::Lib     => [
                    "vDesk/Tasks/Tasks.php"
                ]
            ]
        ]
    ];
    
    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        //Uninstall old Machines.
        $Package = new \vDesk\Packages\Tasks();
        Modules::Machines()::Uninstall($Package);
        
        //Patch files.
        self::Undeploy();
        self::Deploy($Phar, $Path);
        
        //Install new Machines.
        Modules::Machines()::Install($Package);
    }
}