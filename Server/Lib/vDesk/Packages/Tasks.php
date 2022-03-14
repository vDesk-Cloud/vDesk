<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\Archive\Element;
use vDesk\Configuration\Settings;
use vDesk\Machines\IPackage;
use vDesk\Security\AccessControlList;
use vDesk\Struct\Guid;

/**
 * Tasks Package manifest class.
 *
 * @package vDesk\Tasks
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Tasks extends Package implements IPackage {
    
    /**
     * The name of the Package.
     */
    public const Name = "Tasks";
    
    /**
     * The version of the Package.
     */
    public const Version = "1.1.0";
    
    /**
     * The name of the Package.
     */
    public const Vendor = "Kerry <DevelopmentHero@gmail.com>";
    
    /**
     * The name of the Package.
     */
    public const Description = "Package providing a task dispatcher.";
    
    /**
     * The dependencies of the Package.
     */
    public const Dependencies = ["Machines" => "1.0.2"];
    
    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Server => [
            self::Lib => [
                "vDesk/Tasks"
            ]
        ]
    ];
    
    /**
     * The Machines of the Package.
     */
    public const Machines = [
        "Tasks" => "/vDesk/Tasks/Tasks.php"
    ];
    
    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        //Create Tasks folder.
        $Machines = new Element(Settings::$Local["Machines"]["Directory"]);
        $Tasks    = new Element(
            null,
            \vDesk::$User,
            $Machines,
            "Tasks",
            Element::Folder,
            new \DateTime("now"),
            Guid::Create(),
            null,
            null,
            0,
            null,
            new AccessControlList($Machines->AccessControlList)
        );
        $Tasks->Save();
        Settings::$Local["Tasks"] = new Settings\Local\Settings(["Directory" => $Tasks->ID], "Tasks");
        
        //Extract files.
        self::Deploy($Phar, $Path);
    }
    
    /**
     * @inheritDoc
     */
    public static function Uninstall(string $Path): void {
        //Delete files.
        self::Undeploy();
    }
}