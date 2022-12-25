<?php
declare(strict_types=1);

namespace vDesk\MetaInformation\DataSet;

use vDesk\Events\PublicEvent;
use vDesk\MetaInformation\DataSet;

/**
 * Event that occurs when a DataSet has been deleted.
 *
 * @package vDesk\MetaInformation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Deleted extends PublicEvent {

    /**
     * The name of the Event.
     */
    public const Name = "vDesk.MetaInformation.DataSet.Deleted";

    /**
     * Initializes a new instance of the Deleted Event.
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