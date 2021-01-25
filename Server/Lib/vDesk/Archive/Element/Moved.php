<?php
declare(strict_types=1);

namespace vDesk\Archive\Element;

use vDesk\Events\PublicEvent;

/**
 * Represents an Event that occurs when an {@link \vDesk\Archive\Element} has been moved within the archive.
 *
 * @package vDesk\Archive
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Moved extends PublicEvent {
    
    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Archive.Element.Moved";
    
    /**
     * @inheritdoc
     */
    public function ToDataView() {
        return ["ID" => $this->Arguments->ID, "Target" => $this->Arguments->Parent->ID];
    }
    
}
