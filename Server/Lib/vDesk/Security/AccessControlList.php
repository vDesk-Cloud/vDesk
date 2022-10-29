<?php
declare(strict_types=1);

namespace vDesk\Security;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\Expression\λ;
use vDesk\Data\IDNullException;
use vDesk\Data\ICollectionModel;
use vDesk\Security\AccessControlList\Entry;
use vDesk\Struct\Collections\Typed\Observable\Collection;

/**
 * Represents an AccessControlList (ACL), defining permissions to users/groups for access controlled objects.
 *
 * @property int                                            $ID     (write once) Gets or sets the ID of the AccessControlList.
 * @property-read \vDesk\Security\AccessControlList\Entry[] Users   Gets a Generator that iterates over the User related Entries of the AccessControlList.
 * @property-read \vDesk\Security\AccessControlList\Entry[] Groups  Gets a Generator that iterates over the Group related Entries of the AccessControlList.
 * @property-read bool                                      $Read   Gets a value indicating whether the current User has read permissions on the AccessControlList.
 * @property-read bool                                      $Write  Gets a value indicating whether the current User has write permissions on the AccessControlList.
 * @property-read bool                                      $Delete Gets a value indicating whether the current User has delete permissions on the AccessControlList.
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class AccessControlList extends Collection implements ICollectionModel {

    /**
     * The Type of the AccessControlList.
     */
    public const Type = Entry::class;

    /**
     * Flag indicating whether the AccessControlList has been accessed.
     *
     * @var bool
     */
    private bool $Accessed = false;

    /**
     * The added Entries of the AccessControlList.
     *
     * @var \vDesk\Security\AccessControlList\Entry[]
     */
    private array $Added = [];

    /**
     * The deleted Entries of the AccessControlList.
     *
     * @var \vDesk\Security\AccessControlList\Entry[]
     */
    private array $Deleted = [];

    /**
     * Initializes a new instance of the AccessControlList class.
     *
     * @param iterable|\vDesk\Security\AccessControlList\Entry[]|null $Entries Initializes the AccessControlList with the specified Collection of Entries.
     * @param null|int                                                $ID      Initializes the AccessControlList with the specified ID.
     * @param bool                                                    $Read    Initializes the AccessControlList with the specified read permission of the current User.
     * @param bool                                                    $Write   Initializes the AccessControlList with the specified write permission of the current User.
     * @param bool                                                    $Delete  Initializes the AccessControlList with the specified delete permission of the current User.
     */
    public function __construct(
        iterable       $Entries = [],
        protected ?int $ID = null,
        protected bool $Read = true,
        protected bool $Write = true,
        protected bool $Delete = true
    ) {
        parent::__construct($Entries);
        $this->AddProperties([
            "ID"     => [
                \Get => fn(): ?int => $this->ID,
                \Set => fn(int $Value) => $this->ID ??= $Value
            ],
            "Users"  => [
                \Get => function(): \Generator {
                    foreach($this as $Entry) {
                        if($Entry->User instanceof User) {
                            yield $Entry;
                        }
                    }
                }
            ],
            "Groups" => [
                \Get => function(): \Generator {
                    foreach($this as $Entry) {
                        if($Entry->Group instanceof Group) {
                            yield $Entry;
                        }
                    }
                }
            ],
            "Read"   => [
                \Get => function(): bool {
                    if($this->Read === null) {
                        $this->GetPermissions();
                    }
                    return $this->Read;
                }
            ],
            "Write"  => [
                \Get => function(): bool {
                    if($this->Write === null) {
                        $this->GetPermissions();
                    }
                    return $this->Write;
                }
            ],
            "Delete" => [
                \Get => function(): bool {
                    if($this->Delete === null) {
                        $this->GetPermissions();
                    }
                    return $this->Delete;
                }
            ]
        ]);

        $this->OnAdd[] = function(Entry $Entry): void {
            //Check first if an Entry with given User- or Group-ID already exists.
            if($this->ID !== null && $Entry->ID === null) {
                $this->Added[] = $Entry;
            }
        };

        $this->OnRemove[] = function(Entry $Entry): void {
            //Skip mandatory Entries.
            if(
                ($Entry->User->ID !== null && $Entry->User->ID !== User::System)
                || ($Entry->Group->ID !== null && $Entry->Group->ID !== Group::Everyone)
            ) {
                return;
            }
            //Check if the AccessControlList and Entry is not virtual.
            if($this->ID !== null && $Entry->ID !== null) {
                $this->Deleted[] = $Entry;
            }
        };
    }

    /** @inheritdoc */
    public function Add($Element): void {
        if(!$this->ContainsEntry($Element)) {
            parent::Add($Element);
        }
    }

    /**
     * Fills the AccessControlList with its values from the database.
     *
     * @param null|\vDesk\Security\User $User The optional User to fill the permissions of the AccessControlList according the Entries of the AccessControlList.
     *
     * @return \vDesk\Security\AccessControlList The filled AccessControlList.
     * @throws \vDesk\Data\IDNullException Thrown if the AccessControlList is virtual.
     */
    public function Fill(?User $User = null): AccessControlList {

        if($this->ID === null) {
            throw new IDNullException("Cannot Fill Model without ID");
        }

        // Stop/disable dispatching of events.
        $this->Dispatching(false);

        if($this->Count() > 0) {
            $this->Clear();
        }
        foreach(
            Expression::Select("*")
                      ->From("Security.AccessControlListEntries")
                      ->Where(["AccessControlList" => $this->ID])
            as
            $Entry
        ) {
            $this->Add(
                new Entry(
                    (int)$Entry["ID"],
                    new Group($Entry["Group"] !== null ? (int)$Entry["Group"] : null),
                    new User($Entry["User"] !== null ? (int)$Entry["User"] : null),
                    (bool)$Entry["Read"],
                    (bool)$Entry["Write"],
                    (bool)$Entry["Delete"]
                )
            );
            if((int)$Entry["User"] === ($User->ID ?? User::$Current->ID)) {
                if((bool)$Entry["Read"]) {
                    $this->Read = true;
                }
                if((bool)$Entry["Write"]) {
                    $this->Write = true;
                }
                if((bool)$Entry["Delete"]) {
                    $this->Delete = true;
                }
            }
        }
        if($User !== null && (!$this->Read || !$this->Write || !$this->Delete)) {
            $this->GetPermissions($User);
        }

        $this->Accessed = true;

        // Start/re-enable dispatching of events.
        $this->Dispatching(true);
        return $this;

    }

    /**
     * Gets the permissions of the current User according the Entries of the AccessControlList.
     *
     * @param null|\vDesk\Security\User $User The User to get the permissions of; if omitted, loads the permissions of the current User.
     *
     * @throws \vDesk\Data\IDNullException Thrown if the AccessControlList is virtual.
     */
    private function GetPermissions(?User $User = null): void {
        if($this->ID === null) {
            throw new IDNullException();
        }
        $User ??= User::$Current;

        //Avoid useless DB access.
        if($User->ID === User::System) {
            $this->Read   = true;
            $this->Write  = true;
            $this->Delete = true;
            return;
        }

        $Permissions  = Expression
            ::Select(
                [λ::Max("Read"), "Read"],
                [λ::Max("Write"), "Write"],
                [λ::Max("Delete"), "Delete"]
            )
            ->From(
                Expression
                    ::Select(
                        "Read",
                        "Write",
                        "Delete"
                    )
                    ->From("Security.AccessControlListEntries")
                    ->InnerJoin("Security.GroupMemberships")
                    ->On(["AccessControlListEntries.Group" => "GroupMemberships.Group"])
                    ->Where([
                        "AccessControlListEntries.AccessControlList" => $this->ID,
                        "GroupMemberships.User"                      => $User
                    ])
                    ->Union(
                        Expression
                            ::Select(
                                "Read",
                                "Write",
                                "Delete"
                            )
                            ->From(["Security.AccessControlListEntries" => "Entries"])
                            ->Where([
                                "Entries.AccessControlList" => $this->ID,
                                "Entries.User"              => $User
                            ])
                    ),
                "Permissions"
            )
            ->Execute()
            ->ToMap();
        $this->Read   = (bool)($Permissions["Read"] ?? false);
        $this->Write  = (bool)($Permissions["Write"] ?? false);
        $this->Delete = (bool)($Permissions["Delete"] ?? false);
    }

    /**
     * Saves possible changes if a valid ID has been supplied, or creates a new database-entry if none were supplied.
     */
    public function Save(): void {

        if($this->ID !== null) {

            //Save new entries.
            foreach($this->Added as $Added) {
                //Retrieve Entry ID.
                $Added->ID = Expression::Insert()
                                       ->Into("Security.AccessControlListEntries")
                                       ->Values([
                                           "ID"                => null,
                                           "AccessControlList" => $this->ID,
                                           "Group"             => $Added->Group,
                                           "User"              => $Added->User,
                                           "Read"              => $Added->Read,
                                           "Write"             => $Added->Write,
                                           "Delete"            => $Added->Delete
                                       ])
                                       ->ID();
            }

            //Update changed entries.
            foreach($this->Elements as $Updated) {
                //Skip mandatory Entries.
                if(($Updated->User->ID !== null && $Updated->User->ID !== User::System)) {
                    continue;
                }
                if($Updated->Changed && $Updated->User->ID) {
                    Expression::Update("Security.AccessControlListEntries")
                              ->Set([
                                  "Read"   => $Updated->Read,
                                  "Write"  => $Updated->Write,
                                  "Delete" => $Updated->Delete
                              ])
                              ->Where(["ID" => $Updated->ID])
                              ->Execute();
                }
            }

            //Delete removed entries.
            foreach($this->Deleted as $Deleted) {
                Expression::Delete()
                          ->From("Security.AccessControlListEntries")
                          ->Where(["ID" => $Deleted->ID])
                          ->Execute();
            }
        } else {
            //New standard ACL.
            $this->ID = Expression::Insert()
                                  ->Into("Security.AccessControlLists")
                                  ->Values(["ID" => null])
                                  ->ID();

            //Create "System" User Entry.
            $Entry = $this->Find(static fn(Entry $Entry): bool => $Entry->User->ID === User::System);
            if($Entry !== null) {
                $Entry->Read   = true;
                $Entry->Write  = true;
                $Entry->Delete = true;
            } else {
                //Create "System" User Entry.
                $Entry     = Entry::FromUser();
                $Entry->ID = Expression::Insert()
                                       ->Into("Security.AccessControlListEntries")
                                       ->Values([
                                           "ID"                => null,
                                           "AccessControlList" => $this->ID,
                                           "Group"             => null,
                                           "User"              => $Entry->User,
                                           "Read"              => $Entry->Read,
                                           "Write"             => $Entry->Write,
                                           "Delete"            => $Entry->Delete
                                       ])
                                       ->ID();
                $this->Add($Entry);
            }

            //Create "Everyone" Group Entry.
            if(!$this->Any(fn(Entry $Entry): bool => $Entry->Group->ID === Group::Everyone)) {
                $EveryoneGroupEntry     = Entry::FromGroup();
                $EveryoneGroupEntry->ID = Expression::Insert()
                                                    ->Into("Security.AccessControlListEntries")
                                                    ->Values([
                                                        "ID"                => null,
                                                        "AccessControlList" => $this->ID,
                                                        "Group"             => $EveryoneGroupEntry->Group,
                                                        "User"              => null,
                                                        "Read"              => $EveryoneGroupEntry->Read,
                                                        "Write"             => $EveryoneGroupEntry->Write,
                                                        "Delete"            => $EveryoneGroupEntry->Delete
                                                    ])
                                                    ->ID();
                $this->Add($EveryoneGroupEntry);
            }

            /** @var Entry $Entry */
            foreach($this as $Entry) {
                //Retrieve Entry ID.
                $Entry->ID = Expression::Insert()
                                       ->Into("Security.AccessControlListEntries")
                                       ->Values([
                                           "ID"                => null,
                                           "AccessControlList" => $this->ID,
                                           "Group"             => $Entry->Group,
                                           "User"              => $Entry->User,
                                           "Read"              => $Entry->Read,
                                           "Write"             => $Entry->Write,
                                           "Delete"            => $Entry->Delete
                                       ])
                                       ->ID();
            }
        }
    }

    /**
     * Deletes the AccessControlList.
     */
    public function Delete(): void {
        if($this->ID !== null) {
            Expression::Delete()
                      ->From("Security.AccessControlListEntries")
                      ->Where(["AccessControlList" => $this->ID])
                      ->Execute();
            Expression::Delete()
                      ->From("Security.AccessControlLists")
                      ->Where(["ID" => $this->ID])
                      ->Execute();
        }
    }

    /**
     * Creates an AccessControlList from a specified data view.
     *
     * @param array $DataView The data to use to create an AccessControlList.
     *
     * @return \vDesk\Security\AccessControlList An AccessControlList created from the specified data view.
     */
    public static function FromDataView(mixed $DataView): AccessControlList {
        return new static(
            (static function() use ($DataView): \Generator {
                foreach($DataView["Entries"] ?? [] as $Data) {
                    yield Entry::FromDataView($Data);
                }
            })(),
            $DataView["ID"] ?? null
        );
    }

    /**
     * Creates a data view of the AccessControlList.
     *
     * @param bool $Reference Flag indicating whether the data view should represent only a reference of the AccessControlList.
     *
     * @return array The data view representing the current state of the AccessControlList.
     */
    public function ToDataView(bool $Reference = false): array {
        $DataView = [
            "ID"     => $this->ID,
            "Read"   => $this->Read,
            "Write"  => $this->Write,
            "Delete" => $this->Delete
        ];
        if(!$Reference) {
            $DataView["Entries"] = $this->Reduce(
                static function(array $Entries, Entry $Entry): array {
                    $Entries[] = $Entry->ToDataView();
                    return $Entries;
                },
                []
            );
        }
        return $DataView;
    }

    /**
     * Determines whether the AccessControlList contains an Entry that determines that a specified User has read permissions.
     *
     * @param \vDesk\Security\User $User The User to check.
     *
     * @return bool True if the AccessControlList contains an Entry that determines that the specified User has read permissions; otherwise, false.
     */
    public function CanRead(User $User): bool {
        if($User->ID === User::System) {
            return true;
        }
        foreach($this->Users as $Entry) {
            if($Entry->User->ID === $User->ID && $Entry->Read) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determines whether the AccessControlList contains an Entry that determines that a specified User has write permissions.
     *
     * @param \vDesk\Security\User $User The User to check.
     *
     * @return bool True if the AccessControlList contains an Entry that determines that the specified User has write permissions; otherwise, false.
     */
    public function CanWrite(User $User): bool {
        if($User->ID === User::System) {
            return true;
        }
        foreach($this->Users as $Entry) {
            if($Entry->User->ID === $User->ID && $Entry->Write) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determines whether the AccessControlList contains an Entry that determines that a specified User has delete permissions.
     *
     * @param \vDesk\Security\User $User The User to check.
     *
     * @return bool True if the AccessControlList contains an Entry that determines that the specified User has delete permissions; otherwise, false.
     */
    public function CanDelete(User $User): bool {
        if($User->ID === User::System) {
            return true;
        }
        foreach($this->Users as $Entry) {
            if($Entry->User->ID === $User->ID && $Entry->Delete) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determines whether an Entry currently exists in the AccessControlList.
     *
     * @param \vDesk\Security\AccessControlList\Entry $Entry The Entry to check for existence.
     *
     * @return bool True if an Entry with the same User or Group ID already exists in the AccessControlList; otherwise, false.
     */
    private function ContainsEntry(Entry $Entry): bool {
        return $this->Find(
                static fn(Entry $Compare): bool => $Compare->Group->ID === $Entry->Group->ID
                                                   && $Compare->User->ID === $Entry->User->ID
            ) !== null;
    }

    /** @inheritdoc */
    public function ID(): ?int {
        return $this->ID;
    }

    /** @inheritdoc */
    public function Find(callable $Predicate): ?Entry {
        return parent::Find($Predicate);
    }

    /** @inheritdoc */
    public function Remove($Element): Entry {
        return parent::Remove($Element);
    }

    /** @inheritdoc */
    public function RemoveAt(int $Index): Entry {
        return parent::RemoveAt($Index);
    }

    /** @inheritdoc */
    public function offsetGet($Index): Entry {
        if(!$this->Accessed && $this->ID !== null) {
            $this->Fill();
        }
        return parent::offsetGet($Index);
    }
}
