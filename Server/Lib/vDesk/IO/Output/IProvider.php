<?php
declare(strict_types=1);

namespace vDesk\IO\Output;

/**
 * Interface for classes to write data to the current API's output stream.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
interface IProvider {

    /**
     * Writes the specified data to the current output API.
     *
     * @param mixed $Data The data to write.
     */
    public static function Write(mixed $Data): void;

}