<?php
declare(strict_types=1);

namespace vDesk\Events;

/**
 * Represents an interface for dispatchable global Events.
 *
 * @package vDesk\Events
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
abstract class GlobalEvent extends Event {
    
    /**
     * Saves the data of the Event to the database.
     */
    abstract public function Save(): void;
    
}