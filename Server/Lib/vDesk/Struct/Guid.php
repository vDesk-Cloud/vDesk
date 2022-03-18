<?php
declare(strict_types=1);

namespace vDesk\Struct;

/**
 * Utility class for generating V4 Globally unique identifiers.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Guid {

    /**
     * Creates a V4 Globally unique identifier.
     *
     * @return string The string representation of a valid V4 Guid.
     */
    public static function Create(): string {

        if(\function_exists("com_create_guid") === true) {
            return \trim(\com_create_guid(), "{}");
        }

        return \sprintf(
            '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
            Number::Random(0, 65535),
            Number::Random(0, 65535),
            Number::Random(0, 65535),
            Number::Random(16384, 20479),
            Number::Random(32768, 49151),
            Number::Random(0, 65535),
            Number::Random(0, 65535),
            Number::Random(0, 65535)
        );
    }

}