<?php
declare(strict_types=1);

namespace vDesk\Updates;

/**
 * Updates Update manifest class.
 *
 * @package vDesk\Updates
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Updates extends Update {

    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\Updates::class;

    /**
     * The required version of the Update.
     */
    public const RequiredVersion = "1.0.0";

    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Fixed Package manifest.
Description;

    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        self::Deploy($Phar, $Path);
    }
}