<?php
declare(strict_types=1);

namespace vDesk\Archive\Element;

use vDesk\Events\PublicEvent;

/**
 * Represents an Event that occurs when a new {@link \vDesk\Archive\Element} has been added to the archive.
 *
 * @package Archive
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class Created extends PublicEvent {

    /**
     * The name of the Event.
     */
    public const Name = "vDesk.Archive.Element.Created";

    /**
     * @inheritdoc
     */
    public function ToDataView(){
        return [
            "ID"     => $this->Arguments->ID,
            "Target" => $this->Arguments->Parent->ID
        ];
    }

}
