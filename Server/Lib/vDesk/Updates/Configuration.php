<?php
declare(strict_types=1);

namespace vDesk\Updates;

/**
 * Configuration Update manifest class.
 *
 * @package vDesk\Configuration
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Configuration extends Update {

    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\Configuration::class;

    /**
     * The required version of the Update.
     */
    public const RequiredVersion = "1.0.1";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Added compatibility to DataProvider-1.0.0.
Description;

    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        //Just update Package manifest.
    }
}