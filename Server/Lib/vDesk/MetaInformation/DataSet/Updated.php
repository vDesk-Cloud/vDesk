<?php
declare(strict_types=1);

namespace vDesk\MetaInformation\DataSet;

use vDesk\Events\PublicEvent;

/**
 * Represents an Event that occurs when a {@link \vDesk\MetaInformation\DataSet} has been updated.
 *
 * @package vDesk\MetaInformation\DataSet
 * @author  Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class Updated extends PublicEvent {

    /**
     * The name of the Event.
     */
    public const Name = "vDesk.MetaInformation.DataSet.Updated";

    /**
     * @inheritdoc
     */
    public function ToDataView() {
        return ["ID" => $this->Arguments->ID];
    }

}