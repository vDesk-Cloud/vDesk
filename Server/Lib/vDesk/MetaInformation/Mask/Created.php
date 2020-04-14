<?php
declare(strict_types=1);

namespace vDesk\MetaInformation\Mask;

use vDesk\Events\PublicEvent;

/**
 * Represents an Event that occurs when a new {@link \vDesk\MetaInformation\Mask} has been created.
 *
 * @package vDesk\MetaInformation\Mask
 * @author  Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class Created extends PublicEvent {

    /**
     * The name of the Event.
     */
    public const Name = "vDesk.MetaInformation.Mask.Created";

    /**
     * @inheritdoc
     */
    public function ToDataView() {
        return ["ID" => $this->Arguments->ID];
    }

}
