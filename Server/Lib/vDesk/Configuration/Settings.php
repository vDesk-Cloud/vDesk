<?php
declare(strict_types=1);

namespace vDesk\Configuration;

use vDesk\Configuration\Settings\Local;
use vDesk\Configuration\Settings\Remote;
use vDesk\Struct\StaticSingleton;

/**
 * The settings of the system.
 *
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Settings extends StaticSingleton {
    
    /**
     * @var \vDesk\Configuration\Settings\Local
     */
    public static Local $Local;
    
    /**
     * @var \vDesk\Configuration\Settings\Remote
     */
    public static Remote $Remote;
    
    /**
     * Initializes a new instance of the Settings class.
     *
     * @param iterable $Local  Initializes the Settings with the specified Dictionary of local Settings.
     * @param iterable $Remote Initializes the Settings with the specified Dictionary of remote Settings.
     */
    public static function _construct(iterable $Local = [], iterable $Remote = []) {
        self::$Local  = new Local($Local);
        self::$Remote = new Remote($Remote);
    }
}

new Settings();