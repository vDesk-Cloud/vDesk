<?php
declare(strict_types=1);

namespace vDesk\Archive\Element;

use vDesk\Archive\Element;
use vDesk\Events\PublicEvent;

/**
 * Event that occurs when an Element has been moved within the Archive.
 *
 * @package vDesk\Archive
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Moved extends PublicEvent {

    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Archive.Element.Moved";

    /**
     * Initializes a new instance of the Moved Event.
     *
     * @param \vDesk\Archive\Element $Element Initializes the Event with the specified Element.
     */
    public function __construct(public Element $Element) {
    }

    /** @inheritDoc */
    public function ToDataView(): array {
        return ["ID" => $this->Element->ID, "Target" => $this->Element->Parent->ID];
    }

}
