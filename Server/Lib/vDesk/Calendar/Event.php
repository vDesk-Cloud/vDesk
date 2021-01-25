<?php
declare(strict_types=1);

namespace vDesk\Calendar;

use vDesk\Calendar\Event\Participants;
use vDesk\DataProvider\Expression;
use vDesk\DataProvider\MappedGetter;
use vDesk\DataProvider\MappedSetter;
use vDesk\Data\IDNullException;
use vDesk\Data\Model;
use vDesk\Security\AccessControlledModel;
use vDesk\Security\AccessControlList;
use vDesk\Security\User;
use vDesk\Struct\Type;

/**
 * Represents a Calendar Event.
 *
 * @property int                                $ID             (write once) Gets or sets the ID of the Event.
 * @property \DateTime                          $Start          Gets or sets the beginning of the Event.
 * @property \DateTime                          $End            Gets or sets the end of the Event.
 * @property bool                               $FullTime       Gets or sets a value indicating whether the Event lasts the entire day.
 * @property bool                               $Repeating      Gets a value indicating whether the Event occurs more than once.
 * @property int                                $RepeatAmount   Gets or sets the amount of times the Event repeats.
 * @property int                                $RepeatInterval Gets or sets the interval of days the Event is repeating.
 * @property \vDesk\Security\User               $Owner          (write once) Gets or sets the owner of the Event.
 * @property string                             $Title          Gets or sets the title of the Event.
 * @property string                             $Color          Gets or sets the color of the Event.
 * @property string                             $Content        Gets or sets the content of the Event.
 * @property \vDesk\Calendar\Event\Participants $Participants   Gets or sets the Participants of the Event.
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @Todo    Consider renaming "RepeatAmount" to "Repeat" and "RepeatInterval" to "Interval".
 */
class Event extends AccessControlledModel {
    
    /**
     * Flag indicating whether the start of the Event hast been changed.
     *
     * @var bool
     */
    private bool $StartChanged = false;
    
    /**
     * Flag indicating whether the end of the Event hast been changed.
     *
     * @var bool
     */
    private bool $EndChanged = false;
    
    /**
     * Flag indicating whether the full time flag of the Event hast been changed.
     *
     * @var bool
     */
    private bool $FullTimeChanged = false;
    
    /**
     * Flag indicating whether the repeat interval of the Event hast been changed.
     *
     * @var bool
     */
    private bool $RepeatIntervalChanged = false;
    
    /**
     * Flag indicating whether the repeat amount of the Event hast been changed.
     *
     * @var bool
     */
    private bool $RepeatAmountChanged = false;
    
    /**
     * Flag indicating whether the title of the Event hast been changed.
     *
     * @var bool
     */
    private bool $TitleChanged = false;
    
    /**
     * Flag indicating whether the color of the Event has been changed.
     *
     * @var bool
     */
    private bool $ColorChanged = false;
    
    /**
     * Flag indicating whether the content of the Event hast been changed.
     *
     * @var bool
     */
    private bool $ContentChanged = false;
    
    /**
     * Initializes a new instance of the Event class.
     *
     * @param int|null                                $ID                Initializes the Event with the specified ID.
     * @param \vDesk\Security\User|null               $Owner             Initializes the Event with the specified owner.
     * @param \DateTime|null                          $Start             Initializes the Event with the specified start \DateTime.
     * @param \DateTime|null                          $End               Initializes the Event with the specified end \DateTime.
     * @param bool                                    $FullTime          Flag indicating whether the Event is full time.
     * @param int|null                                $RepeatAmount      Initializes the Event with the specified repeat amount.
     * @param int|null                                $RepeatInterval    Initializes the Event with the specified repeat interval.
     * @param string|null                             $Title             Initializes the Event with the specified title.
     * @param string|null                             $Color             Initializes the Event with the specified color.
     * @param string|null                             $Content           Initializes the Event with the specified content.
     * @param \vDesk\Calendar\Event\Participants|null $Participants      Initializes the Event with the specified Collection of Participants.
     * @param \vDesk\Security\AccessControlList|null  $AccessControlList Initializes the Event with the specified AccessControlList.
     */
    public function __construct(
        private    ?int $ID = null,
        private    ?User $Owner = null,
        private    ?\DateTime $Start = null,
        private    ?\DateTime $End = null,
        private    ?bool $FullTime = false,
        private    ?int $RepeatAmount = null,
        private    ?int $RepeatInterval = null,
        private    ?string $Title = null,
        private    ?string $Color = null,
        private    ?string $Content = null,
        private    ?Participants $Participants = null,
        ?AccessControlList $AccessControlList = null
    ) {
        parent::__construct($AccessControlList);
        $this->AddProperties([
            "ID"             => [
                \Get => fn(): ?int => $this->ID,
                \Set => fn(int $Value) => $this->ID ??= $Value
            ],
            "Owner"          => [
                \Get => MappedGetter::Create(
                    $this->Owner,
                    User::class,
                    true,
                    $this->ID,
                    Expression::Select("Owner")
                              ->From("Calendar.Events")
                ),
                \Set => fn(User $Value) => $this->Owner ??= $Value
            ],
            "Start"          => [
                \Get => MappedGetter::Create(
                    $this->Start,
                    \DateTime::class,
                    true,
                    $this->ID,
                    Expression::Select("Start")
                              ->From("Calendar.Events")
                ),
                \Set => function(\DateTime $Value): void {
                    $this->StartChanged = $this->ID !== null && $this->Start != $Value;
                    $this->Start        = $Value;
                }
            ],
            "End"            => [
                \Get => MappedGetter::Create(
                    $this->End,
                    \DateTime::class,
                    true,
                    $this->ID,
                    Expression::Select("End")
                              ->From("Calendar.Events")
                ),
                \Set => function(\DateTime $Value): void {
                    $this->EndChanged = $this->ID !== null && $this->End != $Value;
                    $this->End        = $Value;
                }
            ],
            "FullTime"       => [
                \Get => MappedGetter::Create(
                    $this->FullTime,
                    Type::Bool,
                    true,
                    $this->ID,
                    Expression::Select("FullTime")
                              ->From("Calendar.Events")
                ),
                \Set => MappedSetter::Create(
                    $this->FullTime,
                    Type::Bool,
                    false,
                    $this->ID,
                    $this->FullTimeChanged
                )
            ],
            "Repeating"      => [
                \Get => fn(): bool => ($this->RepeatAmount ?? 0) > 0
            ],
            "RepeatAmount"   => [
                \Get => MappedGetter::Create(
                    $this->RepeatAmount,
                    Type::Int,
                    true,
                    $this->ID,
                    Expression::Select("RepeatAmount")
                              ->From("Calendar.Events")
                ),
                \Set => MappedSetter::Create(
                    $this->RepeatAmount,
                    Type::Int,
                    false,
                    $this->ID,
                    $this->RepeatAmountChanged
                )
            ],
            "RepeatInterval" => [
                \Get => MappedGetter::Create(
                    $this->RepeatInterval,
                    Type::Int,
                    true,
                    $this->ID,
                    Expression::Select("RepeatInterval")
                              ->From("Calendar.Events")
                ),
                \Set => MappedSetter::Create(
                    $this->RepeatInterval,
                    Type::Int,
                    false,
                    $this->ID,
                    $this->RepeatIntervalChanged
                )
            ],
            "Title"          => [
                \Get => MappedGetter::Create(
                    $this->Title,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("Title")
                              ->From("Calendar.Events")
                ),
                \Set => MappedSetter::Create(
                    $this->Title,
                    Type::String,
                    false,
                    $this->ID,
                    $this->TitleChanged
                )
            ],
            "Color"          => [
                \Get => MappedGetter::Create(
                    $this->Color,
                    Type::String,
                    false,
                    $this->ID,
                    Expression::Select("Color")
                              ->From("Calendar.Events")
                ),
                \Set => MappedSetter::Create(
                    $this->Color,
                    Type::String,
                    false,
                    $this->ID,
                    $this->ColorChanged
                )
            ],
            "Content"        => [
                \Get => MappedGetter::Create(
                    $this->Content,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("Content")
                              ->From("Calendar.Events")
                ),
                \Set => MappedSetter::Create(
                    $this->Content,
                    Type::String,
                    true,
                    $this->ID,
                    $this->ContentChanged
                )
            ],
            "Participants"   => [
                \Get => fn(): ?Participants => $this->Participants ??= new Participants(null, $this),
                \Set => function(Participants $Value): void {
                    $this->Participants = $Value;
                    if($this->ID !== null) {
                        $this->Participants->Event = $this;
                    }
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
     * Fills the Event with its values from the database.
     *
     * @param \vDesk\Security\User|null $User The User to determine access on the Event.
     *
     * @return \vDesk\Calendar\Event The filled Event.
     * @throws \vDesk\Data\IDNullException Thrown if the Event is virtual.
     *
     */
    public function Fill(User $User = null): Event {
        if($this->ID === null) {
            throw new IDNullException();
        }
        $Event                   = Expression::Select("*")
                                             ->From("Calendar.Events")
                                             ->Where(["ID" => $this->ID])
                                             ->Execute()
                                             ->ToMap();
        $this->Owner             = new User((int)$Event["Owner"]);
        $this->Start             = new \DateTime($Event["Start"]);
        $this->End               = new \DateTime($Event["End"]);
        $this->FullTime          = (bool)$Event["FullTime"];
        $this->RepeatAmount      = (int)$Event["RepeatAmount"];
        $this->RepeatInterval    = (int)$Event["RepeatInterval"];
        $this->Title             = $Event["Title"];
        $this->Color             = $Event["Color"];
        $this->Content           = $Event["Content"];
        $this->AccessControlList = new AccessControlList([], (int)$Event["AccessControlList"]);
        parent::Fill($User);
        return $this;
    }
    
    /**
     * Saves possible changes if a valid ID was supplied, or creates a new database-entry if none was supplied.
     *
     * @param \vDesk\Security\User|null $User The User to optionally perform access control checks.
     */
    public function Save(User $User = null): void {
        parent::Save($User);
        if($this->ID !== null) {
            Expression::Update("Calendar.Events")
                      ->SetIf([
                          "Start"          => [$this->StartChanged => $this->Start],
                          "End"            => [$this->EndChanged => $this->End],
                          "FullTime"       => [$this->FullTimeChanged => $this->FullTime],
                          "RepeatAmount"   => [$this->RepeatAmountChanged => $this->RepeatAmount],
                          "RepeatInterval" => [$this->RepeatIntervalChanged => $this->RepeatInterval],
                          "Title"          => [$this->TitleChanged => $this->Title],
                          "Color"          => [$this->ColorChanged => $this->Color],
                          "Content"        => [$this->ContentChanged => $this->Content]
                      ])
                      ->Where(["ID" => $this->ID])
                      ->Execute();
            //($this->Participants ?? new Model())->Save();
        } else {
            $this->ID = Expression::Insert()
                                  ->Into("Calendar.Events")
                                  ->Values([
                                      "ID"                => null,
                                      "Owner"             => $this->Owner,
                                      "Start"             => $this->Start,
                                      "End"               => $this->End,
                                      "FullTime"          => $this->FullTime,
                                      "RepeatAmount"      => $this->RepeatAmount,
                                      "RepeatInterval"    => $this->RepeatInterval,
                                      "Title"             => $this->Title,
                                      "Color"             => $this->Color,
                                      "Content"           => $this->Content,
                                      "AccessControlList" => $this->AccessControlList
                                  ])
                                  ->ID();
            /*if($this->Participants !== null) {
                $this->Participants->Event = $this;
                ($this->Participants ?? new Model())->Save();
            }*/
        }
    }
    
    /**
     * Deletes the Event.
     *
     * @param \vDesk\Security\User|null $User The User to optionally perform access control checks.
     */
    public function Delete(User $User = null): void {
        if($this->ID !== null) {
            parent::Delete($User);
            //($this->Participants ?? new Model())->Delete();
            Expression::Delete()
                      ->From("Calendar.Events")
                      ->Where(["ID" => $this->ID])
                      ->Execute();
        }
    }
    
    /**
     * Creates an Event from a specified data view.
     *
     * @param array $DataView The data to use to create an Event.
     *
     * @return \vDesk\Calendar\Event An Event created from the specified data view.
     */
    public static function FromDataView(mixed $DataView): Event {
        $Event                    = new static(
            $DataView["ID"] ?? null,
            new User($DataView["Owner"] ?? null),
            new \DateTime($DataView["Start"] ?? "now"),
            new \DateTime($DataView["End"] ?? "now"),
            $DataView["FullTime"] ?? false,
            $DataView["RepeatAmount"] ?? 0,
            $DataView["RepeatInterval"] ?? 0,
            $DataView["Title"] ?? "",
            $DataView["Color"] ?? "",
            $DataView["Content"] ?? ""
        
        );
        $Event->AccessControlList = AccessControlList::FromDataView($DataView["AccessControlList"]);
        return $Event;
    }
    
    /**
     * Creates a data view of the Event.
     *
     * @param bool $Reference Flag indicating whether the data view should represent only a reference of the Event.
     *
     * @return array The data view representing the current state of the Event.
     */
    public function ToDataView(bool $Reference = false): array {
        return $Reference
            ? ["ID" => $this->ID]
            : [
                "ID"                => $this->ID,
                "Owner"             => ($this->Owner ?? new Model())->ToDataView(true),
                "Start"             => $this->Start->format(\DateTime::ATOM),
                "End"               => $this->End->format(\DateTime::ATOM),
                "FullTime"          => $this->FullTime,
                "RepeatAmount"      => $this->RepeatAmount,
                "RepeatInterval"    => $this->RepeatInterval,
                "Title"             => $this->Title,
                "Color"             => $this->Color,
                "Content"           => $this->Content,
                "AccessControlList" => $this->AccessControlList->ToDataView(true)
            ];
    }
    
    /**
     * Returns the ID of the {@link \vDesk\Security\AccessControlList} assigned to this Event.
     *
     * @return null|int The ID of the {@link \vDesk\Security\AccessControlList}.
     */
    protected function GetACLID(): ?int {
        return $this->ACLID ??= $this->ID !== null
            ? (int)Expression::Select("AccessControlList")
                             ->From("Calendar.Events")
                             ->Where(["ID" => $this->ID])()
            : null;
    }
}
