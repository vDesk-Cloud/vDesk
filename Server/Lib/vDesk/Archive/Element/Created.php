<?php
declare(strict_types=1);

namespace vDesk\Archive\Element;

use vDesk\Archive\Element;
use vDesk\Events\PublicEvent;

/**
 * Event that occurs when a new Element has been added to the Archive.
 *
 * @package vDesk\Archive
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Created extends PublicEvent {

    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Archive.Element.Created";

    /**
     * Initializes a new instance of the Created Event.
     *
     * @param \vDesk\Archive\Element $Element Initializes the Event with the specified Element.
     */
    public function __construct(public Element $Element) {
    }

    /** @inheritDoc */
    public function ToDataView(): Element {
        return $this->Element;
    }

}
