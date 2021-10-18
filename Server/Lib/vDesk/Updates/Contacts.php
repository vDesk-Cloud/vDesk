<?php
declare(strict_types=1);

namespace vDesk\Updates;

/**
 * Contacts Update manifest class.
 *
 * @package vDesk\Contacts
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Contacts extends Update {

    /**
     * The Package of the Update.
     */
    public const Package = \vDesk\Packages\Contacts::class;

    /**
     * The required Package version of the Update.
     */
    public const RequiredVersion = "1.0.0";

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