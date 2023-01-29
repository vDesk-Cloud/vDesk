<?php
declare(strict_types=1);

namespace vDesk\MetaInformation\DataSet;

use vDesk\Events\PublicEvent;
use vDesk\MetaInformation\DataSet;

/**
 * Event that occurs when a new DataSet has been created.
 *
 * @package vDesk\MetaInformation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Created extends PublicEvent {

    /**
     * The name of the Event.
     */
    public const Name = "vDesk.MetaInformation.DataSet.Created";

    /**
     * Initializes a new instance of the Created Event.
     *
     * @param \vDesk\MetaInformation\DataSet $DataSet Initializes the Event with the specified DataSet.
     */
    public function __construct(public DataSet $DataSet) {
    }

    /** @inheritdoc */
    public function ToDataView(): array {
        return ["ID" => $this->DataSet->ID, "Element" => $this->DataSet->Element->ID, "Mask" => $this->DataSet->Mask->ID];
    }

}