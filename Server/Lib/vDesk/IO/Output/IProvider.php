<?php
declare(strict_types=1);

namespace vDesk\IO\Output;

/**
 * Interface IProvider that represents a ...
 *
 * @package vDesk\Connection\Output
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
interface IProvider {
    
    /**
     * Writes the specified data to the current output API.
     *
     * @param mixed $Data The data to write.
     */
    public static function Write($Data): void;
    
}