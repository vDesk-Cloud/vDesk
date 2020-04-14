<?php
declare(strict_types=1);

namespace vDesk\Calendar\Event;

use vDesk\Calendar\Event;
use vDesk\DataProvider\Expression;
use vDesk\DataProvider\MappedGetter;
use vDesk\DataProvider\MappedSetter;
use vDesk\Data\IDataView;
use vDesk\Data\IDNullException;
use vDesk\Data\IManagedModel;
use vDesk\Security\User;
use vDesk\Struct\Properties;
use vDesk\Struct\Property\Getter;
use vDesk\Struct\Type;

/**
 * Represents a participant for an {@link \vDesk\Calendar\Event}.
 *
 * @property int                   $ID      Gets or sets the ID of the Participant.
 * @property \vDesk\Calendar\Event $Event   Gets or sets the ID of the Event of the Participant.
 * @property \vDesk\Security\User  $User    Gets or sets the participating User.
 * @property int                   $Status  Gets or sets the response status.
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class Participant implements IManagedModel {

    use Properties;

    /**
     * The ID of the Participant.
     *
     * @var int|null
     */
    private ?int $ID;

    /**
     * The status of the Participant.
     *
     * @var int|null
     */
    private ?int $Status;

    /**
     * Flag indicating whether the status of the Participant has been changed.
     *
     * @var bool
     */
    private bool $StatusChanged = false;

    /**
     * The User the Participant represents.
     *
     * @var null|\vDesk\Security\User
     */
    private ?User $User;

    /**
     * The Event of the Participant.
     *
     * @var null|\vDesk\Calendar\Event
     */
    private ?Event $Event;

    /**
     * Initializes a new instance of the Participant class.
     *
     * @param int|null                   $ID     Initializes the Participant with the specified ID. @todo Consider identifying Pariticpants by the Event and
     *                                           User in the future.
     * @param \vDesk\Calendar\Event|null $Event  Initializes the Participant with the specified Event.
     * @param \vDesk\Security\User|null  $User   Initializes the Participant with the specified User.
     * @param int|null                   $Status Initializes the Participant with the specified status.
     */
    public function __construct(?int $ID = null, Event $Event = null, User $User = null, int $Status = null) {
        $this->ID     = $ID;
        $this->Event  = $Event;
        $this->User   = $User;
        $this->Status = $Status;
        $this->AddProperties([
            "ID"      => [
                \Get => Getter::Create($this->ID, Type::Int, true),
                \Set => function(int $Value): void {
                    $this->ID = $this->ID ?? $Value;
                }
            ],
            "Event"   => [
                \Get => MappedGetter::Create(
                    $this->Event,
                    Type::Int,
                    true,
                    $this->ID,
                    Expression::Select("Event")
                              ->From("Calendar.Participants")
                ),
                \Set => MappedSetter::Create(
                    $this->Event,
                    Type::Int,
                    false,
                    $this->ID
                )
            ],
            "User"    => [
                \Get => MappedGetter::Create(
                    $this->User,
                    User::class,
                    true,
                    $this->ID,
                    Expression::Select("User")
                              ->From("Calendar.Participants")
                ),
                \Set => function(User $Value): void {
                    $this->User = $this->ID !== null
                        ? $this->User ?? $Value
                        : $Value;
                }
            ],
            "Status"  => [
                \Get => MappedGetter::Create(
                    $this->Status,
                    Type::Int,
                    true,
                    $this->ID,
                    Expression::Select("Status")
                              ->From("Calendar.Participants")
                ),
                \Set => MappedSetter::Create(
                    $this->Status,
                    Type::Int,
                    false,
                    $this->ID,
                    $this->StatusChanged
                )
            ],
            "Changed" => [
                \Get => function(): bool {
                    return $this->ID !== null && $this->StatusChanged;
                }
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function ID(): ?int {
        return $this->ID;
    }

    /**
     * Creates a data view of the Participant.
     *
     * @param bool $Reference
     *
     * @return array The data view representing the current state of the Participant.
     */
    public function ToDataView(bool $Reference = false): array {
        return [
            "ID"     => $this->ID,
            "Event"  => $this->Event->ToDataView(true),
            "User"   => $this->User->ToDataView(true),
            "Status" => $this->Status
        ];
    }

    /**
     * Creates an Participant from a specified data view.
     *
     * @param array $DataView The data to use to create an Participant.
     *
     * @return \vDesk\Calendar\Event\Participant An Participant created from the specified data view.
     */
    public static function FromDataView($DataView): Participant {
        return new static(
            $DataView["ID"] ?? null,
            Event::FromDataView($DataView["Event"] ?? []),
            User::FromDataView($DataView["User"] ?? []),
            $DataView["Status"] ?? 0
        );
    }

    /**
     * Fills the Participant with its values stored in the database.
     *
     * @throws \vDesk\Data\IDNullException Thrown if the Participant is virtual.
     *
     * @return \vDesk\Calendar\Event\Participant The filled Participant.
     */
    public function Fill(): Participant {
        if($this->ID === null) {
            throw new IDNullException();
        }
        $Participant = Expression::Select("Event", "User", "Status")
                                 ->From("Calendar.Participants")
                                 ->Where(["ID" => $this->ID])
                                 ->Execute()
                                 ->ToMap();
        $this->Event  = new Event((int)$Participant["Event"]);
        $this->User   = new User((int)$Participant["User"]);
        $this->Status = (int)$Participant["Status"];
        return $this;
    }
}
