<?php
declare(strict_types=1);

namespace vDesk\Updates;

/**
 * PinBoard Update manifest class.
 *
 * @package vDesk\PinBoard
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class PinBoard extends Update {
    
    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\PinBoard::class;
    
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