<?php
declare(strict_types=1);

namespace vDesk\Security\User;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\Expression\λ;
use vDesk\Data\IDNullException;
use vDesk\Data\IManagedModel;
use vDesk\Security\Group;
use vDesk\Security\User;
use vDesk\Struct\InvalidOperationException;

/**
 * Class that represents a proxy to the "CheckRights" method of the Security Module with caches the specific permissions of an User.
 *
 * @package vDesk\Security\User
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Permissions implements \ArrayAccess, IManagedModel {
    
    /**
     * The permissions of the User.
     *
     * @var bool[]
     */
    private array $Permissions = [];
    
    /**
     * Initializes a new instance of the Permissions class.
     *
     * @param null|\vDesk\Security\User $User Initializes the Permissions with the specified User.
     */
    public function __construct(protected ?User $User = null) {}
    
    /**
     * @inheritDoc
     */
    public function ID(): ?User {
        return $this->User;
    }
    
    /**
     * Fills the Permissions with its values from the database.
     *
     * @return \vDesk\Security\User\Permissions The filled Permissions.
     * @throws \vDesk\Data\IDNullException Thrown if the User of the Permissions is virtual.
     *
     */
    public function Fill(): Permissions {
        if($this->User === null || $this->User->ID === null) {
            throw new IDNullException();
        }
        if($this->User->ID === User::System) {
            foreach(
                Expression::Select("*")
                          ->From("Security.Groups")
                          ->Where(["ID" => Group::Everyone])
                as
                $Group
            ) {
                foreach($Group as $Name => $Permission) {
                    if($Name === "ID" || $Name === "Name") {
                        continue;
                    }
                    $this->Permissions[$Name] = true;
                }
            }
            return $this;
        }
        foreach(
            Expression::Select("*")
                      ->From("Security.Groups")
                      ->InnerJoin("Security.GroupMemberships")
                      ->On([
                          "GroupMemberships.User"  => $this->User,
                          "GroupMemberships.Group" => "Groups.ID"
                      ])
            as
            $Group
        ) {
            foreach($Group as $Name => $Permission) {
                if($Name === "ID" || $Name === "Name" || $Name === "Group" || $Name === "User") {
                    continue;
                }
                $this->Permissions[$Name] = (bool)$Permission;
            }
        }
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function offsetExists($Permission) {
        return isset($this->Permissions[$Permission]);
    }
    
    /**
     * @inheritDoc
     */
    public function offsetGet($Permission) {
        if($this->User->ID === User::System) {
            return true;
        }
        if(!$this->offsetExists($Permission)) {
            $this->Permissions[$Permission] = (bool)Expression::Select([λ::MAX("Groups.{$Permission}"), "Permission"])
                                                              ->From(["Security.Groups" => "Groups"])
                                                              ->InnerJoin("Security.GroupMemberships", "Membership")
                                                              ->On([
                                                                  "Membership.User"  => $this->User,
                                                                  "Membership.Group" => "Groups.ID"
                                                              ])();
        }
        return $this->Permissions[$Permission];
    }
    
    /**
     * @inheritDoc
     * @throws \vDesk\Struct\InvalidOperationException
     */
    public function offsetSet($Permission, $Value) {
        throw new InvalidOperationException();
    }
    
    /**
     * @inheritDoc
     * @throws \vDesk\Struct\InvalidOperationException
     */
    public function offsetUnset($Permission) {
        throw new InvalidOperationException();
    }
    
    /**
     * Creates a Permissions Dictionary from a specified data view.
     * This method will return just an empty Permissions Dictionary because it shouldn't be possible "manipulating" permissions from user land code.
     * To get full access, log in with the system User instead or initialize this class passing an instance of the User class representing the system User.
     *
     * @param array $DataView The data to use to create a Permissions Dictionary.
     *
     * @return \vDesk\Security\User\Permissions A Permissions Dictionary created from the specified data view.
     */
    public static function FromDataView(mixed $DataView): Permissions {
        return new static();
    }
    
    /**
     * Creates a data view of the Permissions.
     *
     * @param bool $Reference Flag indicating whether the data view should represent only a reference of the Permissions.
     *
     * @return array The data view representing the current state of the Permissions.
     */
    public function ToDataView(bool $Reference = false): array {
        return $this->Permissions;
    }
    
}