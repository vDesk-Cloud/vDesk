<?php
declare(strict_types=1);

namespace vDesk\Security\User;

use vDesk\DataProvider\Expression;
use vDesk\Data\IDNullException;
use vDesk\Data\ICollectionModel;
use vDesk\Security\Group;
use vDesk\Security\User;
use vDesk\Struct\Collections\Typed\Observable\Collection;

/**
 * Represents the {@link \vDesk\Security\Group} memberships of an {@link \vDesk\Security\User}.
 *
 * @property-read int                  $Count  Gets the amount of elements in the Collection<Group>Membership.
 * @property \vDesk\Security\User|null $User   (set once) Gets or sets the ID of the belonging {@link \vDesk\Security\User} of the
 *           Groups.
 * @package vDesk\Security
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Groups extends Collection implements ICollectionModel {
    
    /**
     * The Type of the Groups Collection.
     */
    public const Type = Group::class;
    
    /**
     * The the User of the Groups Collection.
     *
     * @var \vDesk\Security\User|null
     */
    private ?User $User;
    
    /**
     * The added Groups of the Groups Collection.
     *
     * @var \vDesk\Security\Group[]
     */
    private array $Added = [];
    
    /**
     * The deleted Groups of the Groups Collection.
     *
     * @var \vDesk\Security\Group[]
     */
    private array $Deleted = [];
    
    /**
     * Initializes a new instance of the Groups class.
     *
     * @param \vDesk\Security\Group[]|null $Groups Initializes the Groups Collection with the specified set of elements.
     * @param \vDesk\Security\User|null    $User   Initializes the Groups Collection with the specified User.
     */
    public function __construct(?iterable $Groups = [], User $User = null) {
        parent::__construct($Groups);
        $this->User = $User;
        $this->AddProperty(
            "User",
            [
                \Get => fn(): ?User => $this->User,
                \Set => fn(User $Value) => $this->User ??= $Value
            ]
        );
        
        /**
         * Listens on the 'OnDelete'-event.
         *
         * @param \vDesk\Security\Groups $Sender
         * @param \vDesk\Security\Group  $Group
         */
        $this->OnAdd[] = function(&$Sender, Group $Group): void {
            if($this->User !== null && $this->User->ID !== null) {
                $this->Added[] = $Group;
            }
        };
        
        /**
         * Listens on the 'OnDelete'-event.
         *
         * @param \vDesk\Security\Groups $Sender
         * @param \vDesk\Security\Group  $Group
         */
        $this->OnDelete[] = function(&$Sender, Group $Group): void {
            if(
                $this->User !== null
                && $this->User->ID !== null
                && $Group->ID !== Group::Everyone
            ) {
                $this->Deleted[] = $Group;
            }
        };
        $this->Added      = $this->Elements;
    }
    
    /**
     * @inheritDoc
     */
    public function ID(): ?int {
        return $this->User->ID;
    }
    
    /**
     * Fills the Groups with all Groups the specified User is a member of.
     *
     * @param \vDesk\Security\User $User The User to fetch the memberships of.
     *
     * @return \vDesk\Security\User\Groups A Collection of all Groups the specified User is a member of.
     */
    public static function FromUser(User $User): Groups {
        return (new static([], $User))->Fill();
    }
    
    /**
     * Fills the Groups with all Groups the specified User is a member of.
     *
     * @param int $ID The ID of the User to fetch the memberships of.
     *
     * @return \vDesk\Security\User\Groups A Collection of all Groups the specified User is a member of.
     */
    public static function FromUserID(int $ID): Groups {
        return (new static([], new User($ID)))->Fill();
    }
    
    /**
     * @inheritdoc
     */
    public function Find(callable $Predicate): ?Group {
        return parent::Find($Predicate);
    }
    
    /**
     * @inheritdoc
     */
    public function Remove($Element): Group {
        return parent::Remove($Element);
    }
    
    /**
     * @inheritdoc
     */
    public function RemoveAt(int $Index): Group {
        return parent::RemoveAt($Index);
    }
    
    /**
     * @inheritdoc
     */
    public function offsetGet($Index): Group {
        return parent::offsetGet($Index);
    }
    
    /**
     * Fills the Groups Collection with its values from the database.
     *
     * @return \vDesk\Security\User\Groups The filled Groups Collection.
     * @throws \vDesk\Data\IDNullException Thrown if the User of the Groups Collection is virtual.
     */
    public function Fill(): Groups {
        if($this->User === null || $this->User->ID === null) {
            throw new IDNullException();
        }
        $this->StopDispatch();
        foreach(
            Expression::Select("Group")
                      ->From("Security.GroupMemberships")
                      ->Where(["User" => $this->User])
                      ->OrderBy(["Group" => true])
            as
            $Group
        ) {
            $this->Add(new Group((int)$Group["Group"]));
        }
        $this->StartDispatch();
        return $this;
    }
    
    /**
     * Saves possible changes if an ID of a valid {@link \vDesk\Security\User} has been supplied.
     */
    public function Save(): void {
        if($this->User !== null && $this->User->ID !== null && $this->Count() > 0) {
            //Check if the "everyone" Group is in the collection, otherwise append it to the collection.
            if(!$this->Any(static fn(Group $Group): bool => $Group->ID === Group::Everyone)) {
                $this->Add(new Group(Group::Everyone));
            }
            
            //Save added Groups.
            foreach($this->Added as $Added) {
                Expression::Insert()
                          ->Into("Security.GroupMemberships")
                          ->Values([
                              "Group" => $Added->ID,
                              "User"  => $this->User
                          ])
                          ->Execute();
            }
            //Delete removed Groups.
            foreach($this->Deleted as $Deleted) {
                Expression::Delete()
                          ->From("Security.GroupMemberships")
                          ->Where([
                              "Group" => $Deleted->ID,
                              "User"  => $this->User
                          ])
                          ->Execute();
            }
        }
    }
    
    /**
     * Deletes all {@link \vDesk\Security\Group} memberships of the associated {@link \vDesk\Security\User}.
     */
    public function Delete(): void {
        if($this->User !== null && $this->User->ID !== null) {
            Expression::Delete()
                      ->From("Security.GroupMemberships")
                      ->Where(["User" => $this->User])
                      ->Execute();
        }
    }
    
    /**
     * Creates a Groups from a specified data view.
     *
     * @param array $DataView The data to use to create a Groups.
     *
     * @return \vDesk\Security\User\Groups A Groups created from the specified data view.
     */
    public static function FromDataView(mixed $DataView): Groups {
        return new static(
            (static function() use ($DataView) {
                foreach($DataView as $ID) {
                    yield new Group($ID);
                }
            })()
        );
    }
    
    /**
     * Creates a data view of the Groups.
     *
     * @param bool $Reference Flag indicating whether the data view should represent only a reference of the Groups.
     *
     * @return array The data view representing the current state of the Groups.
     */
    public function ToDataView(bool $Reference = false): array {
        if($this->Count === 0 && $this->User !== null) {
            $this->Fill();
        }
        return $this->Reduce(
            static function(array $Groups, Group $Group) use ($Reference): array {
                $Groups[] = $Group->ToDataView($Reference);
                return $Groups;
            },
            []
        );
    }
    
}