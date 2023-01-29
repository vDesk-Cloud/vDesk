<?php
declare(strict_types=1);

namespace vDesk\MetaInformation\DataSet;

use vDesk\Events\PublicEvent;
use vDesk\MetaInformation\DataSet;

/**
 * Event that occurs when a DataSet has been updated.
 *
 * @package vDesk\MetaInformation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Updated extends PublicEvent {

    /**
     * The name of the Event.
     */
    public const Name = "vDesk.MetaInformation.DataSet.Updated";

    /**
     * Initializes a new instance of the Updated Event.
     *
     * @param \vDesk\MetaInformation\DataSet $DataSet Initializes the Event with the specified DataSet.
     */
    public function __construct(public DataSet $DataSet) {
    }

    /** @inheritdoc */
    public function ToDataView(): DataSet {
        return $this->DataSet;
    }

}