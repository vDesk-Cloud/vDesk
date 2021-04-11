<?php
declare(strict_types=1);

namespace vDesk\Updates;

/**
 * MetaInformation Update manifest class.
 *
 * @package vDesk\MetaInformation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class MetaInformation extends Update {
    
    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\MetaInformation::class;
    
    /**
     * The required version of the Update.
     */
    public const RequiredVersion = "1.0.1";
    
    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Fixed deletion of wrong database.
Description;
    
    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        //Do nothing.
    }
}