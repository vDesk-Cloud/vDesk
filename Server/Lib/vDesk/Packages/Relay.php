<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\Configuration\Settings;
use vDesk\Machines\IPackage;

/**
 * Relay Package manifest class.
 *
 * @package vDesk\Relay
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Relay extends Package implements IPackage {
    
    /**
     * The name of the Package.
     */
    public const Name = "Relay";
    
    /**
     * The version of the Package.
     */
    public const Version = "1.0.0";
    
    /**
     * The name of the Package.
     */
    public const Vendor = "Kerry <DevelopmentHero@gmail.com>";
    
    /**
     * The name of the Package.
     */
    public const Description = "Package providing a distributed event system dispatcher.";
    
    /**
     * The dependencies of the Package.
     */
    public const Dependencies = ["Machines" => "1.0.1", "vDesk" => "1.0.1", "Security" => "1.0.1"];
    
    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Server => [
            self::Lib => [
                "vDesk/Relay"
            ]
        ]
    ];
    
    /**
     * The Machines of the Package.
     */
    public const Machines = [
        "/vDesk/Relay/Server.php",
        "/vDesk/Relay/Client.php"
    ];
    
    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        //Create Package configuration.
        Settings::$Local["Relay"] = new Settings\Local\Settings(
            [
                "Port"     => 3420,
                "Server"   => "",
                "Client"   => "",
                "Password" => ""
            ],
            "Relay"
        );
        
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