<?php
declare(strict_types=1);

namespace vDesk\Updates;

/**
 * Interface for Modules capable of installing Updates.
 *
 * @package vDesk\Updates
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
interface IModule {
    
    /**
     * Installs a specified Update.
     *
     * @param \vDesk\Updates\Update $Update The Update to install.
     * @param \Phar                 $Phar   The Phar archive of the Update.
     * @param string                $Path   The installation path of the Update.
     */
    public static function Update(Update $Update, \Phar $Phar, string $Path): void;
    
}