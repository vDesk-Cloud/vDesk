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
 * Class that represents a collection of Group-memberships of an User.
 *
 * @property \vDesk\Security\User|null $User   (set once) Gets or sets the ID of the belonging {@link \vDesk\Security\User} of the Groups.
 * @package vDesk\Security
 * @author  Kerry <DevelopmentHero@gmail.com>
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

        $this->OnAdd[] = function(Group $Group): void {
            if($this->User !== null && $this->User->ID !== null) {
                $this->Added[] = $Group;
            }
        };

        $this->OnRemove[] = function(Group $Group): void {
            if(
                $this->User !== null
                && $this->User->ID !== null
                && $Group->ID !== Group::Everyone
            ) {
                $this->Deleted[] = $Group;
            }
        };

        $this->Added = $this->Elements;
    }

    /** @inheritDoc */
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

    /** @inheritDoc */
    public function Find(callable $Predicate): ?Group {
        return parent::Find($Predicate);
    }

    /** @inheritDoc */
    public function Remove($Element): Group {
        return parent::Remove($Element);
    }

    /** @inheritDoc */
    public function RemoveAt(int $Index): Group {
        return parent::RemoveAt($Index);
    }

    /** @inheritDoc */
    public function offsetGet($Index): Group {
        return parent::offsetGet($Index);
    }

    /** @inheritdoc */
    public function Fill(): Groups {
        if($this->User?->ID === null) {
            throw new IDNullException();
        }
        $this->Dispatching(false);
        foreach(
            Expression::Select("GroupMemberships.Group", "Groups.Name")
                      ->From("Security.GroupMemberships")
                      ->InnerJoin("Security.Groups")
                      ->On(["GroupMemberships.Group" => "Groups.ID"])
                      ->Where(["User" => $this->User])
                      ->OrderBy(["Group" => true])
            as
            $Group
        ) {
            $this->Add(new Group((int)$Group["Group"], $Group["Name"]));
        }
        $this->Dispatching(true);
        return $this;
    }

    /** @inheritdoc */
    public function Save(): void {
        if($this->User !== null && $this->User->ID !== null && $this->Count() > 0) {
            //Check if the "everyone" Group is in the Collection, append it otherwise.
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

    /** @inheritdoc */
    public function Delete(): void {
        if($this->User !== null && $this->User->ID !== null) {
            Expression::Delete()
                      ->From("Security.GroupMemberships")
                      ->Where(["User" => $this->User])
                      ->Execute();
        }
    }

    /** @inheritdoc */
    public static function FromDataView(mixed $DataView): Groups {
        return new static(
            (static function() use ($DataView) {
                foreach($DataView as $ID) {
                    yield new Group($ID);
                }
            })()
        );
    }

    /** @inheritdoc */
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