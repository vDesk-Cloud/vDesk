<?php
declare(strict_types=1);

namespace vDesk\Security;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\MappedGetter;
use vDesk\DataProvider\MappedSetter;
use vDesk\Data\IDataView;
use vDesk\Data\IDNullException;
use vDesk\Data\IModel;
use vDesk\Security\Group\Permissions;
use vDesk\Struct\Properties;
use vDesk\Struct\Type;

/**
 * Represents a group of Users and its {@link \vDesk\Security\Permission}s.
 * Provides functionality for updating existing and saving new groups.
 *
 * @property int                                $ID          Gets the ID of the Group.
 * @property string                             $Name        Gets or sets the name of the Group.
 * @property  \vDesk\Security\Group\Permissions $Permissions Gets or sets the permissions of the Group.
 * @property  \vDesk\Security\Group\Users       $Users       Gets the Users of the Group.
 * @package vDesk\Security
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Group implements IModel {

    use Properties;

    /**
     * @var int The ID of the 'everyone' Group.
     */
    public const Everyone = 1;

    /**
     *  Flag indicating whether the name of a non virtual Group has been changed.
     *
     * @var bool
     */
    private bool $NameChanged = false;

    /**
     * The member Users of the Group.
     *
     * @var null|\vDesk\Security\Group\Users
     */
    private ?\vDesk\Security\Group\Users $Users = null;

    /**
     * Initializes a new instance of the Group class.
     *
     * @param int|null                               $ID          Initializes the Group with the specified ID.
     * @param string|null                            $Name        Initializes the Group with the specified name.
     * @param null|\vDesk\Security\Group\Permissions $Permissions Initializes the Group with the specified Collection of Permissions.
     */
    public function __construct(protected ?int $ID = null, protected ?string $Name = null, protected ?Permissions $Permissions = null) {
        $this->AddProperties([
            "ID"          => [
                \Get => fn(): ?int => $this->ID,
                \Set => fn(int $Value) => $this->ID ??= $Value
            ],
            "Name"        => [
                \Get => MappedGetter::Create(
                    $this->Name,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("Name")
                              ->From("Security.Groups")
                ),
                \Set => MappedSetter::Create(
                    $this->Name,
                    Type::String,
                    false,
                    $this->ID,
                    $this->NameChanged
                )
            ],
            "Permissions" => [
                \Get => fn(): ?Permissions => $this->Permissions ??= Permissions::FromGroup($this),
                \Set => fn(Permissions $Value) => $this->Permissions = $Value
            ],
            "Users"       => [
                \Get => function(): ?Group\Users {
                    if($this->Users === null) {
                        $this->Users = new Group\Users([], $this);
                        if($this->ID !== null) {
                            $this->Users->Fill();
                        }
                    }
                    return $this->Users;
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
     * Fills the Group with its values from the database.
     *
     * @return \vDesk\Security\Group The filled Group.
     * @throws \vDesk\Data\IDNullException Thrown if the Group is virtual.
     *
     */
    public function Fill(): Group {
        if($this->ID === null) {
            throw new IDNullException();
        }
        $this->Permissions = new Permissions();
        $Group             = Expression::Select("*")
                                       ->From("Security.Groups")
                                       ->Where(["ID" => $this->ID])
                                       ->Execute()
                                       ->ToMap();
        foreach($Group as $Key => $Value) {
            if($Key === "ID") {
                continue;
            }
            if($Key === "Name") {
                $this->Name = $Value;
            } else {
                $this->Permissions->Add($Key, (bool)$Value);
            }
        }
        return $this;
    }

    /**
     * Saves possible changes if a valid ID was supplied, or creates a new database-entry if none has been supplied.
     */
    public function Save(): void {
        if($this->ID !== null) {
            $Values = [];
            if($this->NameChanged) {
                $Values = ["Name" => $this->Name];
            }
            foreach($this->Permissions as $Permission => $Value) {
                $Values[$Permission] = $Value;
            }
            Expression::Update("Security.Groups")
                      ->Set($Values)
                      ->Where(["ID" => $this->ID])
                      ->Execute();
        } else {
            $Values = [
                "ID"   => null,
                "Name" => $this->Name
            ];
            if($this->Permissions !== null) {
                foreach($this->Permissions as $Permission => $Value) {
                    $Values[$Permission] = $Value;
                }
            }
            $this->ID = Expression::Insert()
                                  ->Into("Security.Groups")
                                  ->Values($Values)
                                  ->ID();
        }
    }

    /**
     * Deletes the Group.
     */
    public function Delete(): void {
        if($this->ID !== null) {
            Expression::Delete()
                      ->From("Security.Groups")
                      ->Where(["ID" => $this->ID])
                      ->Execute();
            Expression::Delete()
                      ->From("Security.AccessControlListEntries")
                      ->Where(["Group" => $this->ID])
                      ->Execute();
        }
    }

    /**
     * Creates a Group from a specified data view.
     *
     * @param array $DataView The data to use to create a Group.
     *
     * @return \vDesk\Security\Group A Group created from the specified data view.
     */
    public static function FromDataView(mixed $DataView): IDataView {
        return new static(
            $DataView["ID"] ?? null,
            $DataView["Name"] ?? "",
            Permissions::FromDataView($DataView["Permissions"] ?? [])
        );
    }

    /**
     * Creates a data view of the Group.
     *
     * @param bool $Reference Flag indicating whether the data view should represent only a reference of the Group.
     *
     * @return array The data view representing the current state of the Group.
     */
    public function ToDataView(bool $Reference = false): array {
        return $Reference
            ? ["ID" => $this->ID, "Name" => $this->Name]
            : [
                "ID"          => $this->ID,
                "Name"        => $this->Name,
                "Permissions" => $this->Permissions->ToDataView()
            ];
    }

}
