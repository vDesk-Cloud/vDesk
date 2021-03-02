<?php
declare(strict_types=1);

namespace vDesk\Packages;


class Reflect extends Package {
    
    /**
     * The name of the Package.
     */
    public const Name = "Reflect";
    
    /**
     * The version of the Package.
     */
    public const Version = "0.0.1";
    
    /**
     * The name of the Package.
     */
    public const Vendor = "Kerry <DevelopmentHero@gmail.com>";
    
    /**
     * The name of the Package.
     */
    public const Description = "Package that provides a reflection based class API generator.";
    
    /**
     * The dependencies of the Package.
     */
    public const Dependencies = ["Pages" => "1.0.1"];
    
    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        // TODO: Implement Install() method.
    }
    
    /**
     * @inheritDoc
     */
    public static function Uninstall(string $Path): void {
        // TODO: Implement Uninstall() method.
    }
}