<?php

declare(strict_types=1);

namespace vDesk\Security\AccessControlList;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\MappedGetter;
use vDesk\DataProvider\MappedSetter;
use vDesk\Data\IDataView;
use vDesk\Data\IDNullException;
use vDesk\Data\IManagedModel;
use vDesk\Security\Group;
use vDesk\Security\User;
use vDesk\Struct\Properties;
use vDesk\Struct\Property\Getter;
use vDesk\Struct\Type;

/**
 * Represents an entry which contains the permissions for a user/group of a {@link \vDesk\Security\AccessControlList}.
 *
 * @author  Kerry
 * @property int                   $ID      Gets or sets the the ID of the Entry.
 * @property \vDesk\Security\Group $Group   Gets or sets the Group of the Entry.
 * @property \vDesk\Security\User  $User    Gets or sets the User of the Entry.
 * @property string                $Name    Gets or sets the Name of the associated user or group.
 * @property bool                  $Read    Gets or sets a value that indicates whether the affected user or group is allowed to read.
 * @property bool                  $Write   Gets or sets a value that indicates whether the affected user or group is allowed to write.
 * @property bool                  $Delete  Gets or sets a value that indicates whether the affected user or group is allowed to delete.
 * @property-read bool             $Changed Gets a value that indicates whether the permissions have been changed or not.
 *                              (This property is only available when a valid EntryID is been passed).
 * @package vDesk\Security
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Entry implements IManagedModel {
    
    use Properties;
    
    /**
     * Defines the systemuser.
     */
    public const SystemUser = 1;
    
    /**
     * Global 'everyone'-group.
     */
    public const GroupEveryone = 2;
    
    /**
     *
     */
    public const GroupEveryonePrivate = 3;
    
    /**
     *  Flag indicating whether the read permissions of the Entry has been changed.
     *
     * @var bool
     */
    private bool $ReadChanged = false;
    
    /**
     * Flag indicating whether the write permissions of the Entry has been changed.
     *
     * @var bool
     */
    private bool $WriteChanged = false;
    
    /**
     *  Flag indicating whether the delete permissions of the Entry has been changed.
     *
     * @var bool
     */
    private bool $DeleteChanged = false;
    
    /**
     * Initializes a new instance of the Entry class.
     *
     * @param null|int                   $ID     Initializes the Entry with the specified ID.
     * @param \vDesk\Security\Group|null $Group  Initializes the Entry with the specified Group.
     * @param \vDesk\Security\User|null  $User   Initializes the Entry with the specified User.
     * @param bool|null                  $Read   Flag indicating whether the Group or User of the Entry has read permissions.
     * @param bool|null                  $Write  Flag indicating whether the Group or User of the Entry has write permissions.
     * @param bool|null                  $Delete Flag indicating whether the Group or User of the Entry has delete permissions.
     */
    public function __construct(
       protected ?int $ID = null,
       protected ?Group $Group = null,
       protected ?User $User = null,
       protected bool $Read = true,
       protected bool $Write = true,
       protected bool $Delete = true
    ) {
        $this->AddProperties([
            "ID"      => [
                \Get => fn(): ?int => $this->ID,
                \Set => fn(int $Value) => $this->ID ??= $Value
            ],
            "Group"   => [
                \Get => MappedGetter::Create(
                    $this->Group,
                    Group::class,
                    true,
                    $this->ID,
                    Expression::Select("Group")
                              ->From("Security.AccessControlListEntries")
                ),
                \Set => fn(Group $Value) => $this->Group = $Value
            ],
            "User"    => [
                \Get => MappedGetter::Create(
                    $this->User,
                    User::class,
                    true,
                    $this->ID,
                    Expression::Select("User")
                              ->From("Security.AccessControlListEntries")
                ),
                \Set => fn(User $Value) => $this->User = $Value
            ],
            "Read"    => [
                \Get => fn(): ?bool => $this->Read,
                \Set => MappedSetter::Create(
                    $this->Read,
                    Type::Bool,
                    false,
                    $this->ID,
                    $this->ReadChanged
                )
            ],
            "Write"   => [
                \Get => fn(): ?bool => $this->Write,
                \Set => MappedSetter::Create(
                    $this->Write,
                    Type::Bool,
                    false,
                    $this->ID,
                    $this->WriteChanged
                )
            ],
            "Delete"  => [
                \Get => fn(): ?bool => $this->Delete,
                \Set => MappedSetter::Create(
                    $this->Delete,
                    Type::Bool,
                    false,
                    $this->ID,
                    $this->DeleteChanged
                )
            ],
            "Changed" => [
                \Get => fn(): bool => ($this->ID !== null ? $this->ReadChanged || $this->WriteChanged || $this->DeleteChanged : false)
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
     * Factory method that creates a new instance of the Entry class representing the specified permissions of the specified User.
     *
     * @param \vDesk\Security\User|null $User   The User of the Entry.
     * @param bool                      $Read   Flag indicating whether the specified User has read permissions.
     * @param bool                      $Write  Flag indicating whether the specified User has write permissions.
     * @param bool                      $Delete Flag indicating whether the specified User has delete permissions.
     *
     * @return \vDesk\Security\AccessControlList\Entry The Entry representing the specified permissions of the specified User.
     */
    public static function FromUser(User $User = null, bool $Read = true, bool $Write = true, bool $Delete = true): Entry {
        return new static(
            null,
            new Group(),
            $User ?? new User(User::System),
            $Read,
            $Write,
            $Delete
        );
    }
    
    /**
     * Factory method that creates a new instance of the Entry class representing the specified permissions of the specified Group.
     *
     * @param \vDesk\Security\Group|null $Group  The Group of the Entry.
     * @param bool                       $Read   Flag indicating whether the specified User has read permissions.
     * @param bool                       $Write  Flag indicating whether the specified User has write permissions.
     * @param bool                       $Delete Flag indicating whether the specified User has delete permissions.
     *
     * @return \vDesk\Security\AccessControlList\Entry The Entry representing the specified permissions of the specified Group.
     */
    public static function FromGroup(Group $Group = null, bool $Read = true, bool $Write = true, bool $Delete = true): Entry {
        return new static(
            null,
            $Group ?? new Group(Group::Everyone),
            new User(),
            $Read,
            $Write,
            $Delete
        );
    }
    
    /**
     * Fills the Entry with its values from the database.
     *
     * @return \vDesk\Security\AccessControlList\Entry The filled Entry.
     * @throws \vDesk\Data\IDNullException Thrown if the Entry is virtual.
     *
     */
    public function Fill(): Entry {
        if($this->ID === null) {
            throw new IDNullException();
        }
        $Entry        = Expression::Select("*")
                                  ->From("Security.AccessControlListEntries")
                                  ->Where(["ID" => $this->ID])
                                  ->Execute()
                                  ->ToMap();
        $this->Group  = new Group($Entry["Group"] !== null ? (int)$Entry["Group"] : null);
        $this->User   = new User($Entry["User"] !== null ? (int)$Entry["User"] : null);
        $this->Read   = (bool)$Entry["Read"];
        $this->Write  = (bool)$Entry["Write"];
        $this->Delete = (bool)$Entry["Delete"];
        return $this;
    }
    
    /**
     * Creates an Entry from a specified data view.
     *
     * @param array $DataView The data to use to create an Entry.
     *
     * @return \vDesk\Security\AccessControlList\Entry An Entry created from the specified data view.
     */
    public static function FromDataView(mixed $DataView): IDataView {
        return new static(
            $DataView["ID"] ?? null,
            Group::FromDataView($DataView["Group"] ?? []),
            User::FromDataView($DataView["User"] ?? []),
            $DataView["Read"] ?? false,
            $DataView["Write"] ?? false,
            $DataView["Delete"] ?? false,
        );
    }
    
    /**
     * Creates a data view of the Entry.
     *
     * @param bool $Reference Flag indicating whether the data view should represent only a reference of the Entry.
     *
     * @return array The data view representing the current state of the Entry.
     */
    public function ToDataView(bool $Reference = false): array {
        return $Reference
            ? ["ID" => $this->ID]
            : [
                "ID"     => $this->ID,
                "Group"  => $this->Group->ToDataView(true),
                "User"   => $this->User->ToDataView(true),
                "Read"   => $this->Read,
                "Write"  => $this->Write,
                "Delete" => $this->Delete
            ];
    }
}
