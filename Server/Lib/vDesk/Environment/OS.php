<?php
declare(strict_types=1);

namespace vDesk\Environment;

/**
 * Enumeration of common operating systems.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class OS {

    /**
     * The current OS on which PHP is running.
     */
    public const Current = \PHP_OS;

    /**
     * Constant expression for NT-based systems.
     */
    public const NT = "WINNT";

    /**
     * Constant expression for Linux-based systems.
     */
    public const Linux = "Linux";

    /**
     * Constant expression for MacOS-based systems.
     */
    public const MacOS = "DAR";

}