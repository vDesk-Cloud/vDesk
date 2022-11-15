<?php
declare(strict_types=1);

namespace vDesk\Archive\Element;

use vDesk\Archive\Element;
use vDesk\Events\PublicEvent;

/**
 * Event that occurs when an Element has been renamed.
 *
 * @package vDesk\Archive
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Renamed extends PublicEvent {

    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Archive.Element.Renamed";

    /**
     * Initializes a new instance of the Renamed Event.
     *
     * @param \vDesk\Archive\Element $Element Initializes the Event with the specified Element.
     */
    public function __construct(public Element $Element) {
    }

    /** @inheritDoc */
    public function ToDataView(): array {
        return ["ID" => $this->Element->ID, "Name" => $this->Element->Name];
    }

}
