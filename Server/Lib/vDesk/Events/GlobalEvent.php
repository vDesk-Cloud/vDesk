<?php
declare(strict_types=1);

namespace vDesk\Events;

use vDesk\Data\IDataView;
use vDesk\Struct\InvalidOperationException;

/**
 * Represents an interface for dispatchable global Events.
 *
 * @package vDesk\Events
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
abstract class GlobalEvent extends Event implements IDataView {
    
    /**
     * Saves the data of the Event to the database.
     */
    abstract public function Save(): void;

    /**
     * @throws \vDesk\Struct\InvalidOperationException Method is not supported
     */
    public static function FromDataView(mixed $DataView): IDataView {
        throw new InvalidOperationException("Creating Events from a DataView isn't supported!");
    }
}