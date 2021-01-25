<?php
declare(strict_types=1);

namespace vDesk\Archive\Element;

use vDesk\Events\PublicEvent;

/**
 * Represents an Event that occurs when an {@link \vDesk\Archive\Element} has been deleted from the Archive.
 *
 * @package vDesk\Archive
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Deleted extends PublicEvent {
    
    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Archive.Element.Deleted";
    
    /**
     * @inheritdoc
     */
    public function ToDataView() {
        return (string)$this->Arguments->ID;
    }
    
}
