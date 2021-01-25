<?php
declare(strict_types=1);

namespace vDesk\Archive\Element;

use vDesk\Events\PublicEvent;

/**
 * Represents an Event that occurs when an {@link \vDesk\Archive\Element} has been renamed.
 *
 * @package vDesk\Archive
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Renamed extends PublicEvent {
    
    /**
     * The name of the Event.
     *
     * @var string
     */
    public const Name = "vDesk.Archive.Element.Renamed";
    
    /**
     * @inheritdoc
     */
    public function ToDataView() {
        return json_encode(["ID" => $this->Arguments->ID, "Name" => $this->Arguments->Name]);
    }
    
}
