<?php
declare(strict_types=1);

namespace vDesk\Modules;

/**
 * Proxy Model that pipes Method calls as a request to different processes.
 * This class is used in the future for the process based version.
 *
 * @package vDesk\Modules
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
final class Proxy extends Module {

    /**
     * Flag indicating whether the Module is running remote.
     */
    public const Remote = true;

    public function __call($name, $arguments) {
        // Make request and send to socket or whatever..
        // This is where it starts gettin' pervert and the real black magic begins.
    }

}