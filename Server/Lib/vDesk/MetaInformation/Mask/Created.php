<?php
declare(strict_types=1);

namespace vDesk\MetaInformation\Mask;

use vDesk\Events\PublicEvent;
use vDesk\MetaInformation\Mask;

/**
 * Event that occurs when a new Mask has been created.
 *
 * @package vDesk\MetaInformation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Created extends PublicEvent {

    /**
     * The name of the Event.
     */
    public const Name = "vDesk.MetaInformation.Mask.Created";

    /**
     * Initializes a new instance of the Created Event.
     *
     * @param \vDesk\MetaInformation\Mask $Mask Initializes the Event with the specified Mask.
     */
    public function __construct(public Mask $Mask) {
    }

    /** @inheritdoc */
    public function ToDataView(): Mask {
        return $this->Mask;
    }

}