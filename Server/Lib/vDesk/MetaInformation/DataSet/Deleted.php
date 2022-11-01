<?php
declare(strict_types=1);

namespace vDesk\MetaInformation\DataSet;

use vDesk\Events\PublicEvent;

/**
 * Represents an Event that occurs when a {@link \vDesk\MetaInformation\DataSet} has been deleted.
 *
 * @package vDesk\MetaInformation\DataSet
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Deleted extends PublicEvent {

    /**
     * The name of the Event.
     */
    public const Name = "vDesk.MetaInformation.DataSet.Deleted";

    /**
     * @inheritdoc
     */
    public function ToDataView(): array {
        return ["ID" => $this->Arguments->ID];
    }

}