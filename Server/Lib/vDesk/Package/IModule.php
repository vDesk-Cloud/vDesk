<?php
declare(strict_types=1);

namespace vDesk\Package;

use vDesk\Package;

/**
 * Interface for Modules capable of installing Packages.
 *
 * @package vDesk\Package
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
interface IModule {
    
    /**
     * Installs a specified Package.
     *
     * @param \vDesk\Package $Package The Package to install.
     * @param \Phar          $Phar    The Phar archive of the Package.
     * @param string         $Path    The installation path of the Package.
     */
    public static function Install(Package $Package, \Phar $Phar, string $Path): void;
    
    /**
     * Uninstalls a specified Package.
     *
     * @param \vDesk\Package $Package The Package to uninstall.
     * @param string         $Path    The installation path of the Package.
     */
    public static function Uninstall(Package $Package, string $Path): void;
    
}