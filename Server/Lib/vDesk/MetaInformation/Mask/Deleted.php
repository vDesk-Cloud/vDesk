<?php
declare(strict_types=1);

namespace vDesk\MetaInformation\Mask;

use vDesk\Events\PublicEvent;
use vDesk\MetaInformation\Mask;

/**
 * Event that occurs when a Mask has been deleted.
 *
 * @package vDesk\MetaInformation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Deleted extends PublicEvent {

    /**
     * The name of the Event.
     */
    public const Name = "vDesk.MetaInformation.Mask.Deleted";

    /**
     * Initializes a new instance of the Deleted Event.
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