<?php
declare(strict_types=1);

namespace vDesk\MetaInformation\DataSet;

use vDesk\Events\PublicEvent;

/**
 * Represents an Event that occurs when a new {@link \vDesk\MetaInformation\DataSet} has been created.
 *
 * @package vDesk\MetaInformation\DataSet
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Created extends PublicEvent {
    
    /**
     * The name of the Event.
     */
    public const Name = "vDesk.MetaInformation.DataSet.Created";
    
    /**
     * @inheritdoc
     */
    public function ToDataView() {
        return ["ID" => $this->Arguments->ID, "Element" => $this->Arguments->Element->ID, "Mask" => $this->Arguments->Mask->ID];
    }
    
}