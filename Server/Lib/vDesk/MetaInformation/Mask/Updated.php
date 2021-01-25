<?php
declare(strict_types=1);

namespace vDesk\MetaInformation\Mask;

use vDesk\Events\PublicEvent;

/**
 * Represents an Event that occurs when a {@link \vDesk\MetaInformation\Mask} has been updated.
 *
 * @package vDesk\MetaInformation\Mask
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Updated extends PublicEvent {
    
    /**
     * The name of the Event.
     */
    public const Name = "vDesk.MetaInformation.Mask.Updated";
    
    /**
     * @inheritdoc
     */
    public function ToDataView() {
        return ["ID" => $this->Arguments->ID];
    }
    
}
