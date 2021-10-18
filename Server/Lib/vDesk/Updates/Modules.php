<?php
declare(strict_types=1);

namespace vDesk\Updates;

/**
 * Modules Update manifest class.
 *
 * @package vDesk\Modules
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Modules extends Update {
    
    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\Modules::class;
    
    /**
     * The required version of the Update.
     */
    public const RequiredVersion = "1.0.0";
    
    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Added compatibility to vDesk-1.0.0.
Description;
    
    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        //Just update Package manifest.
    }
}