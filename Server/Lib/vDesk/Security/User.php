<?php
declare(strict_types=1);

namespace vDesk\Security;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\MappedGetter;
use vDesk\DataProvider\MappedSetter;
use vDesk\Data\IDNullException;
use vDesk\Data\IModel;
use vDesk\Data\Model;
use vDesk\Security\User\Permissions;
use vDesk\Struct\Properties;
use vDesk\Struct\Type;

/**
 * Represents an user.
 * Provides functionality for adding new users and updating the account-information or {@link \vDesk\Security\Group}-memberships of an user.
 *
 * @property int                         $ID               (set once) Gets or sets ID of the User.
 * @property string                      $Name             (set once) Gets or sets the name of the User.
 * @property string                      $Locale           Gets or sets the locale of the User.
 * @property-write string                $Password         (set once) Sets the password of the User.
 * @property string                      $Email            Gets or sets the email-address of the User.
 * @property boolean                     $Active           Gets or sets a value indicating whether the Users is active.
 * @property int                         $FailedLoginCount Gets or sets the amount of failed login-attempts of the User.
 * @property \vDesk\Security\User\Groups $Memberships      Gets or sets the {@link \vDesk\Security\Group}-memberships of the User.
 *           User.
 * @package vDesk\Security
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class User implements IModel {
    
    use Properties;
    
    /**
     * @var int The ID of the systemuser.
     */
    public const System = 1;
    
    /**
     * Flag indicating whether the name of the User has been changed.
     *
     * @var bool
     */
    private bool $NameChanged = false;
    
    /**
     * Flag indicating whether the locale of the User has been changed.
     *
     * @var bool
     */
    private bool $LocaleChanged = false;
    
    /**
     * Flag indicating whether the email of a non virtual User has been changed.
     *
     * @var bool
     */
    private bool $EmailChanged = false;
    
    /**
     * Flag indicating whether the active state of a non virtual User has been changed.
     *
     * @var bool
     */
    private bool $ActiveChanged = false;
    
    /**
     * Flag indicating whether the amount of failed login attempts of a non virtual User has been changed.
     *
     * @var bool
     */
    private bool $FailedLoginCountChanged = false;
    
    /**
     * @var \vDesk\Security\User\Permissions
     */
    public Permissions $Permissions;

    /**
     * The current logged in User.
     *
     * @var null|\vDesk\Security\User
     */
    public static ?User $Current;
    
    /**
     * Initializes a new instance of the User class.
     *
     * @param int|null                         $ID               Initializes the User with the specified ID.
     * @param string|null                      $Name             Initializes the User with the specified name.
     * @param string|null                      $Locale           Initializes the User with the specified locale.
     * @param string|null                      $Password         Initializes the User with the specified password.
     * @param string|null                      $Email            Initializes the User with the specified email.
     * @param bool|null                        $Active           Flag indicating whether the User is active.
     * @param int|null                         $FailedLoginCount Initializes the User with the specified failed login count.
     * @param \vDesk\Security\User\Groups|null $Memberships      Initializes the User with the specified Collection of Groups.
     * @param string|null                      $Ticket           Initializes the User with the specified session ticket.
     */
    public function __construct(
       protected ?int $ID = null,
       protected ?string $Name = null,
       protected ?string $Locale = null,
       protected ?string $Password = null,
       protected ?string $Email = null,
       protected ?bool $Active = null,
       protected ?int $FailedLoginCount = null,
       protected ?\vDesk\Security\User\Groups $Memberships = null,
       public ?string $Ticket = null
    ) {
        $this->Permissions      = new Permissions($this);
        $this->AddProperties([
                "ID"               => [
                    \Get => fn(): ?int => $this->ID,
                    \Set => fn(int $Value) => $this->ID ??= $Value
                ],
                "Name"             => [
                    \Get => MappedGetter::Create(
                        $this->Name,
                        Type::String,
                        true,
                        $this->ID,
                        Expression::Select("Name")
                                  ->From("Security.Users")
                    ),
                    \Set => MappedSetter::Create(
                        $this->Name,
                        Type::String,
                        false,
                        $this->ID,
                        $this->NameChanged
                    )
                ],
                "Locale"           => [
                    \Get => MappedGetter::Create(
                        $this->Locale,
                        Type::String,
                        true,
                        $this->ID,
                        Expression::Select("Locale")
                                  ->From("Security.Users")
                    ),
                    \Set => MappedSetter::Create(
                        $this->Locale,
                        Type::String,
                        false,
                        $this->ID,
                        $this->LocaleChanged
                    )
                ],
                "Password"         => [
                    \Set => fn(string $Value) => $this->Password ??= $Value
                ],
                "Email"            => [
                    \Get => MappedGetter::Create(
                        $this->Email,
                        Type::String,
                        true,
                        $this->ID,
                        Expression::Select("Email")
                                  ->From("Security.Users")
                    ),
                    \Set => MappedSetter::Create(
                        $this->Email,
                        Type::String,
                        false,
                        $this->ID,
                        $this->EmailChanged
                    )
                ],
                "Active"           => [
                    \Get => MappedGetter::Create(
                        $this->Active,
                        Type::Bool,
                        true,
                        $this->ID,
                        Expression::Select("Active")
                                  ->From("Security.Users")
                    ),
                    \Set => MappedSetter::Create(
                        $this->Active,
                        Type::Bool,
                        false,
                        $this->ID,
                        $this->ActiveChanged
                    )
                ],
                "FailedLoginCount" => [
                    \Get => MappedGetter::Create(
                        $this->FailedLoginCount,
                        Type::Int,
                        true,
                        $this->ID,
                        Expression::Select("FailedLoginCount")
                                  ->From("Security.Users")
                    ),
                    \Set => MappedSetter::Create(
                        $this->FailedLoginCount,
                        Type::Int,
                        false,
                        $this->ID,
                        $this->FailedLoginCountChanged
                    )
                ],
                "Memberships"      => [
                    \Get => function(): ?User\Groups {
                        if($this->Memberships === null) {
                            $this->Memberships = new User\Groups([], $this);
                            if($this->ID !== null) {
                                $this->Memberships->Fill();
                            }
                        }
                        return $this->Memberships;
                    },
                    \Set => fn(User\Groups $Value) => $this->Memberships = $Value
                ]
            ]
        );
    }
    
    /**
     * @inheritDoc
     */
    public function ID(): ?int {
        return $this->ID;
    }
    
    /**
     * Factory method that creates an User from a specified name.
     *
     * @param string $Name The name of the User.
     *
     *
     * @return \vDesk\Security\User An User
     */
    public static function FromName(string $Name): User {
        
        $User = Expression::Select(
            "ID",
            "Name",
            "Locale",
            "Email",
            "Active",
            "FailedLoginCount",
            "LastLogin"
        )
                          ->From("Security.Users")
                          ->Where(["Name" => $Name])
                          ->Execute()
                          ->ToMap();
        return new static(
            (int)$User["ID"],
            $User["Name"],
            $User["Locale"],
            null,
            $User["Email"],
            (bool)$User["Active"],
            (int)$User["FailedLoginCount"],
            new User\Groups([], new static((int)$User["ID"]))
        );
    }
    
    /**
     * Factory method that creates an User from a specified name.
     *
     * @param string $Ticket The name of the User.
     *
     * @return \vDesk\Security\User An User
     */
    public static function FromTicket(string $Ticket): User {
        $User = Expression::Select(
            "Users.ID",
            "Users.Name",
            "Users.Locale",
            "Users.Email",
            "Users.Active",
            "Users.FailedLoginCount",
            "Users.LastLogin"
        )
                          ->From("Security.Users")
                          ->InnerJoin("Security.Sessions")
                          ->On(["Users.ID" => "Sessions.User"])
                          ->Where(["Sessions.Ticket" => $Ticket])
                          ->Execute()
                          ->ToMap();
        return new static(
            (int)$User["ID"],
            $User["Name"],
            $User["Locale"],
            null,
            $User["Email"],
            (bool)$User["Active"],
            (int)$User["FailedLoginCount"],
            new User\Groups([], new static((int)$User["ID"])),
            $Ticket
        );
    }
    
    /**
     * Fills the User with its values from the database.
     *
     * @return \vDesk\Security\User The filled User.
     * @throws \vDesk\Data\IDNullException Thrown if the User is virtual.
     *
     */
    public function Fill(): User {
        if($this->ID === null) {
            throw new IDNullException();
        }
        $User                   = Expression::Select(
            "Name",
            "Locale",
            "Email",
            "Active",
            "FailedLoginCount"
        )
                                            ->From("Security.Users")
                                            ->Where(["ID" => $this->ID])
                                            ->Execute()
                                            ->ToMap();
        $this->Name             = $User["Name"];
        $this->Locale           = $User["Locale"];
        $this->Email            = $User["Email"];
        $this->Active           = (bool)$User["Active"];
        $this->FailedLoginCount = (int)$User["FailedLoginCount"];
        $this->Memberships      = (new User\Groups([], $this))->Fill();
        return $this;
    }
    
    /**
     * Saves the model and possible changes or creates a new database-entry.
     */
    public function Save(): void {
        
        //Check if the user is not virtual.
        if($this->ID !== null) {
            if(
                $this->NameChanged
                || $this->LocaleChanged
                || $this->EmailChanged
                || $this->ActiveChanged
                || $this->FailedLoginCountChanged
            ) {
                Expression::Update("Security.Users")
                          ->SetIf([
                              "Name"             => [$this->NameChanged => $this->Name],
                              "Locale"           => [$this->LocaleChanged => $this->Locale],
                              "Email"            => [$this->EmailChanged => $this->Email],
                              "Active"           => [$this->ActiveChanged => $this->Active],
                              "FailedLoginCount" => [$this->FailedLoginCountChanged => $this->FailedLoginCount]
                          ])
                          ->Where(["ID" => $this->ID])
                          ->Execute();
            }
        } else {
            $this->ID = Expression::Insert()
                                  ->Into("Security.Users")
                                  ->Values([
                                      "ID"               => null,
                                      "Name"             => $this->Name,
                                      "Locale"           => $this->Locale,
                                      "Password"         => $this->Password,
                                      "Email"            => $this->Email,
                                      "Active"           => $this->Active,
                                      "FailedLoginCount" => $this->FailedLoginCount
                                  ])
                                  ->ID();
        }
        if($this->Memberships !== null) {
            $this->Memberships->User = $this;
            $this->Memberships->Save();
        }
    }
    
    /**
     * Deletes the User.
     */
    public function Delete(): void {
        if($this->ID !== null && $this->ID !== self::System) {
            Expression::Delete()
                      ->From("Security.Users")
                      ->Where(["ID" => $this->ID])
                      ->Execute();
            Expression::Delete()
                      ->From("Security.GroupMemberships")
                      ->Where(["User" => $this->ID])
                      ->Execute();
            Expression::Delete()
                      ->From("Security.AccessControlListEntries")
                      ->Where(["User" => $this->ID])
                      ->Execute();
        }
    }
    
    /**
     * Creates an User from a specified data view.
     *
     * @param array $DataView The data to use to create an User.
     *
     * @return \vDesk\Security\User An User created from the specified data view.
     */
    public static function FromDataView(mixed $DataView): User {
        return new static(
            $DataView["ID"] ?? null,
            $DataView["Name"] ?? "",
            $DataView["Locale"] ?? "",
            null,
            $DataView["Email"] ?? "",
            $DataView["Active"] ?? false,
            $DataView["FailedLoginCount"] ?? 0,
            User\Groups::FromDataView($DataView["Memberships"] ?? [])
        );
    }
    
    /**
     * Creates a data view of the User.
     *
     * @param bool $Reference Flag indicating whether the data view should represent only a reference of the User.
     *
     * @return array The data view representing the current state of the User.
     */
    public function ToDataView(bool $Reference = false): array {
        if($Reference) {
            return ["ID" => $this->ID];
        }
        return $this->Ticket !== null
            ? [
                "ID"          => $this->ID,
                "Name"        => $this->Name,
                "Locale"      => $this->Locale,
                "Email"       => $this->Email,
                "Ticket"      => $this->Ticket,
                "Permissions" => $this->Permissions?->ToDataView(),
                "Memberships" => $this->Memberships?->Fill()->ToDataView(true)
            ]
            : [
                "ID"               => $this->ID,
                "Name"             => $this->Name,
                "Locale"           => $this->Locale,
                "Email"            => $this->Email,
                "Active"           => $this->Active,
                "FailedLoginCount" => $this->FailedLoginCount,
                "Memberships"      => $this->Memberships?->ToDataView(true)
            ];
    }
}
