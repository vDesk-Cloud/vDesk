<?php
declare(strict_types=1);

namespace vDesk\Calendar\Event;

use vDesk\Calendar\Event;
use vDesk\DataProvider\Expression;
use vDesk\DataProvider;
use vDesk\Data\ICollectionModel;
use vDesk\Data\IDNullException;
use vDesk\Security\User;
use vDesk\Struct\Collections\Typed\Observable\Collection;

/**
 * Represents a Collection of {@link \vDesk\Calendar\Event\Participant}s associated to an {@link \vDesk\Calendar\Event}.
 *
 * @property \vDesk\Calendar\Event|null $Event Gets or sets the Event of the Participants.
 * @package vDesk\Calendar
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Participants extends Collection implements ICollectionModel {
    
    /**
     * The Type of the Participants.
     */
    public const Type = Participant::class;
    
    /**
     * The Event of the Participants.
     *
     * @var \vDesk\Calendar\Event|null
     */
    private ?Event $Event;
    
    /**
     * Flag indicating whether the Participants has been accessed.
     *
     * @var bool
     */
    private bool $Accessed = false;
    
    /**
     * The added Participants of the Participants.
     *
     * @var \vDesk\Calendar\Event\Participant[]
     */
    private array $Added = [];
    
    /**
     * The Event of the Participants.
     *
     * @var \vDesk\Calendar\Event\Participant[]
     */
    private array $Deleted = [];
    
    /**
     * Initializes a new instance of the Participants class.
     *
     * @param \vDesk\Calendar\Event\Participant[]|null $Participants Initializes the Participants with the specified set of Participants.
     * @param \vDesk\Calendar\Event|null               $Event        Initializes the Participants with the specified Event.
     */
    public function __construct(?iterable $Participants = [], Event $Event = null) {
        $this->Event = $Event;
        $this->AddProperty("Event",
            [
                "Event" => [
                    \Get => fn(): ?Event => $this->Event,
                    \Set => fn(Event $Value) => $this->Event ??= $Value
                ],
            ]);
        parent::__construct($Participants);
        
        /**
         * Listens on the 'OnAdd'-event.
         *
         * @param \vDesk\Calendar\Event\Participants $Sender
         * @param \vDesk\Calendar\Event\Participant  $Participant
         */
        $this->OnAdd[] = function(&$Sender, Participant $Participant): void {
            //Check if entry with given user already exists.
            if(
                $this->Event->ID !== null
                && $Participant->ID === null
                && $this->Find(static fn(Participant $Compare): bool => $Compare->User === $Participant->User) === null
            ) {
                $this->Added[] = $Participant;
            }
        };
        
        /**
         * Listens on the 'OnDelete'-event.
         *
         * @param \vDesk\Calendar\Event\Participants $Sender
         * @param \vDesk\Calendar\Event\Participant  $Participant
         */
        $this->OnDelete[] = function(&$Sender, Participant $Participant): void {
            if($this->Event->ID !== null && $Participant->ID !== null) {
                $this->Deleted[] = $Participant;
            }
        };
    }
    
    /**
     * @inheritDoc
     */
    public function ID(): ?Event {
        return $this->Event;
    }
    
    /**
     * @inheritdoc
     */
    public function Find(callable $Predicate): ?Participant {
        return parent::Find($Predicate);
    }
    
    /**
     * @inheritdoc
     */
    public function Remove($Element): Participant {
        return parent::Remove($Element);
    }
    
    /**
     * @inheritdoc
     */
    public function RemoveAt(int $Index): Participant {
        return parent::RemoveAt($Index);
    }
    
    /**
     * @inheritdoc
     */
    public function offsetGet($Index): Participant {
        if(!$this->Accessed && $this->Event->ID !== null) {
            $this->Fill();
        }
        return parent::offsetGet($Index);
    }
    
    /**
     * Creates a Participants from a specified data view.
     *
     * @param array $DataView The data to use to create a Participants.
     *
     * @return \vDesk\Calendar\Event\Participants A Participants created from the specified data view.
     */
    public static function FromDataView(mixed $DataView): Participants {
        return new static(
            (static function() use ($DataView) {
                foreach($DataView as $Data) {
                    yield Participant::FromDataView($Data);
                }
            })()
        );
    }
    
    /**
     * Creates a data view of the Participants.
     *
     * @param bool $Reference Flag indicating whether the data view should represent only a reference of the Participants.
     *
     * @return array The data view representing the current state of the Participants.
     */
    public function ToDataView(bool $Reference = false): array {
        return $this->Reduce(
            static function(array $Participants, Participant $Participant) use ($Reference): array {
                $Participants[] = $Participant->ToDataView($Reference);
                return $Participants;
            },
            []
        );
    }
    
    /**
     * Saves possible changes if a valid ID was supplied, or creates a new database-entry if none was supplied.
     */
    public function Save(): void {
        if($this->Event->ID !== null) {
            
            //Save new entries.
            foreach($this->Added as $Added) {
                
                DataProvider::Call("Calendar.AddParticipant", $this->Event->ID, $Added->User->ID, $Added->Status);
                //Retrieve ID.
                $Added->ID = Expression::Insert()
                                       ->Into("Calendar.Participants")
                                       ->Values([
                                           "ID"    => null,
                                           "Event" => $this->Event->ID,
                                           "User"  => $Added->User->ID
                                       ])
                                       ->ID();
            }
            
            //Update changed Participants.
            foreach($this->Elements as $Updated) {
                if($Updated->Changed) {
                    Expression::Update("Calendar.Participants")
                              ->Set(["Status" => $Updated->Status])
                              ->Where(["ID" => $Updated->ID])
                              ->Execute();
                }
            }
            
            //Delete removed Participants.
            foreach($this->Deleted as $Deleted) {
                Expression::Delete()
                          ->From("Calendar.Participants")
                          ->Where(["ID" => $Deleted->ID])
                          ->Execute();
            }
        }
    }
    
    /**
     * Deletes the all participants of the associated \vDesk\Calendar\Event.
     */
    public function Delete(): void {
        if($this->Event->ID !== null) {
            Expression::Delete()
                      ->From("Calendar.Participants")
                      ->Where(["Event" => $this->Event->ID])
                      ->Execute();
        }
    }
    
    /**
     * Fills the model with all values if a valid ID was supplied.
     *
     * @return \vDesk\Calendar\Event\Participants The filled Participants.
     * @throws \Exception If Fill() is called while no ID were supplied.
     *
     */
    public function Fill(): Participants {
        
        if($this->Event->ID === null) {
            throw new IDNullException();
        }
        
        // Stop/disable event dispatching,
        $this->StopDispatch();
        
        foreach(
            Expression::Select("*")
                      ->From("Calendar.Participants")
                      ->Where(["Event" => $this->Event->ID])
            as
            $Row
        ) {
            $Participant         = new Participant($Row["ID"]);
            $Participant->User   = new User((int)$Row["User"]);
            $Participant->Status = (int)$Row["Status"];
            $this->Add($Participant);
        }
        
        $this->Accessed = true;
        
        // Start/re-enable event dispatching,
        $this->StartDispatch();
        
        return $this;
        
    }
}
