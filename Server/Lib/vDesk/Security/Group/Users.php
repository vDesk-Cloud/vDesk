<?php
declare(strict_types=1);

namespace vDesk\Security\Group;

use vDesk\Data\IDataView;
use vDesk\Data\IDNullException;
use vDesk\Data\IManagedModel;
use vDesk\DataProvider\Expression;
use vDesk\Security\Group;
use vDesk\Security\User;
use vDesk\Struct\Collections\Typed\Collection;

/**
 * Represents a Collection of member Users of a Group.
 *
 * @package vDesk\Security\Group
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Users extends Collection implements IManagedModel {
    
    /**
     * The Type of the Users Collection.
     */
    public const Type = User::class;
    
    /**
     * The the Group of the Users Collection.
     *
     * @var \vDesk\Security\Group|null
     */
    private ?Group $Group;
    
    /**
     * Initializes a new instance of the Groups class.
     *
     * @param \vDesk\Security\User[]|null $Users Initializes the Users Collection with the specified set of Users.
     * @param \vDesk\Security\Group|null  $Group Initializes the Users Collection with the specified Group.
     */
    public function __construct(?iterable $Users = [], Group $Group = null) {
        parent::__construct($Users);
        $this->Group = $Group;
        $this->AddProperty(
            "Group",
            [
                \Get => fn(): ?Group => $this->Group,
                \Set => fn(Group $Value) => $this->Group ??= $Value
            ]
        );
    }
    
    /**
     * @inheritDoc
     */
    public function ID(): ?Group {
        return $this->Group;
    }
    
    /**
     * Fills the Users Collection with its values from the database.
     *
     * @return \vDesk\Security\Group\Users The filled Users Collection.
     * @throws \vDesk\Data\IDNullException Thrown if the Group of the Users Collection is virtual.
     */
    public function Fill(): Users {
        if($this->Group === null || $this->Group->ID === null) {
            throw new IDNullException();
        }
        $this->Clear();
        foreach(
            Expression::Select("User")
                      ->From("Security.GroupMemberships")
                      ->Where(["Group" => $this->Group])
                      ->OrderBy(["User" => true])
            as
            $User
        ) {
            $this->Add(new User((int)$User["User"]));
        }
        return $this;
    }
    
    /**
     * Creates a Users Collection from a specified data view.
     *
     * @param array $DataView The data to use to create a Users Collection.
     *
     * @return \vDesk\Security\Group\Users A Users Collection created from the specified data view.
     */
    public static function FromDataView(mixed $DataView): Users {
        return new static(
            (static function() use ($DataView) {
                foreach($DataView as $ID) {
                    yield new User($ID);
                }
            })()
        );
    }
    
    /**
     * Creates a data view of the Users Collection.
     *
     * @param bool $Reference Flag indicating whether the data view should represent only a reference of the Users Collection.
     *
     * @return array The data view representing the current state of the Users Collection.
     */
    public function ToDataView(bool $Reference = false): array {
        if($this->Count === 0 && $this->Group !== null) {
            $this->Fill();
        }
        return $this->Reduce(
            static function(array $Users, User $User): array {
                $Users[] = $User->ID;
                return $Users;
            },
            []
        );
    }
}