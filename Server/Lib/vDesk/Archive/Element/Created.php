<?php
declare(strict_types=1);

namespace vDesk\Archive\Element;

use vDesk\Events\PublicEvent;

/**
 * Represents an Event that occurs when a new {@link \vDesk\Archive\Element} has been added to the archive.
 *
 * @package vDesk\Archive
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Created extends PublicEvent {
    
    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Archive.Element.Created";
    
    /**
     * @inheritdoc
     */
    public function ToDataView(): array {
        return [
            "ID"     => $this->Arguments->ID,
            "Target" => $this->Arguments->Parent->ID
        ];
    }
    
}
