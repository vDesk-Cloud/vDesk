<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\Configuration\Settings;
use vDesk\DataProvider\Expression;
use vDesk\DataProvider\Expression\λ;
use vDesk\DataProvider\Collation;
use vDesk\DataProvider\Type;
use vDesk\Locale\IPackage;
use vDesk\Modules\Module\Command;
use vDesk\Modules\Module\Command\Parameter;
use vDesk\Security\Group;
use vDesk\Security\User;
use vDesk\Struct\Collections\Observable\Collection;

/**
 * Class Security represents ...
 *
 * @package vDesk\Packages\Packages
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Security extends Package implements IPackage {
    
    /**
     * The name of the Package.
     */
    public const Name = "Security";
    
    /**
     * The version of the Package.
     */
    public const Version = "1.0.0";
    
    /**
     * The name of the Package.
     */
    public const Vendor = "Kerry <DevelopmentHero@gmail.com>";
    
    /**
     * The name of the Package.
     */
    public const Description = "Package providing a group- and user based access control aswell for single access controlled entities.";
    
    /**
     * The dependencies of the Package.
     */
    public const Dependencies = [
        "Configuration" => "1.0.0",
        "Events"        => "1.0.0"
    ];
    
    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Client => [
            self::Design => [
                "vDesk/Security"
            ],
            self::Lib    => [
                "vDesk/Security.js",
                "vDesk/Security"
            ]
        ],
        self::Server => [
            self::Modules => [
                "Security.php"
            ],
            self::Lib     => [
                "vDesk/Security"
            ]
        ]
    ];
    
    /**
     * The translations of the Package.
     */
    public const Locale = [
        "DE" => [
            "Security"    => [
                "AddUser"                => "Benutzer hinzufügen",
                "ChangeEmail"            => "E-Mail-Adresse ändern",
                "DeleteEntry"            => "Eintrag löschen",
                "DeleteGroup"            => "Gruppe löschen",
                "Email"                  => "E-Mail-Addresse",
                "Everyone"               => "Jeder",
                "FailedLogins"           => "Fehlgeschlagene Anmeldungen",
                "GroupEditorChangeGroup" => "Beim wechsel der Gruppe gehen ungespeicherte Änderungen verloren. Gruppe wechseln?",
                "GroupEditorDeleteGroup" => "Beim Löschen einer Gruppe gehen sämtliche Berechtigungen, sowie ACL-Einträge verloren. Gruppe löschen?",
                "Groups"                 => "Gruppen",
                "KeepLoggedin"           => "Angemeldet bleiben",
                "Memberships"            => "Mitgliedschaften",
                "NewGroup"               => "Neue Gruppe",
                "NewPassword"            => "Neues Kennwort",
                "NewUser"                => "Neuer Benutzer",
                "OldPassword"            => "Altes Kennwort",
                "Owner"                  => "Besitzer",
                "Password"               => "Kennwort",
                "Permissions"            => "Berechtigungen",
                "Read"                   => "Lesen",
                "ResetPassword"          => "Kennwort zurücksetzen",
                "Status"                 => "Status",
                "User"                   => "Benutzer",
                "UserConfiguration"      => "Benutzereinstellungen",
                "UserGroup"              => "Benutzer/Gruppe",
                "Username"               => "Benutzername",
                "Users"                  => "Benutzer",
                "Visibility"             => "Sichtbarkeit",
                "Write"                  => "Schreiben",
                "UserCount"              => "Anzahl Benutzerkonten",
                "GroupCount"             => "Anzahl Benutzergruppen"
            ],
            "Permissions" => [
                "ReadAccessControlList"   => "Legt fest ob Mitglieder der Gruppe Zugriffslisten sehen können",
                "UpdateAccessControlList" => "Legt fest ob Mitglieder der Gruppe Zugriffslisten bearbeiten können",
                "CreateGroup"             => "Legt fest ob Mitglieder der Gruppe neue Benutzergruppen erstellen können",
                "UpdateGroup"             => "Legt fest ob Mitglieder der Gruppe Benutzergruppen bearbeiten können",
                "DeleteGroup"             => "Legt fest ob Mitglieder der Gruppe Benutzergruppen löschen können",
                "CreateUser"              => "Legt fest ob Mitglieder der Gruppe neue Benutzerkonten erstellen können",
                "UpdatePassword"          => "Legt fest ob Mitglieder der Gruppe das Kennwort ihres Benutzerkontos ändern können",
                "UpdateUser"              => "Legt fest ob Mitglieder der Gruppe Benutzerkonten bearbeiten können",
                "DeleteUser"              => "Legt fest ob Mitglieder der Gruppe Benutzerkonten löschen können"
            ],
            "Settings"    => [
                "Security:MaxFailedLogins" => "Legt die Anzahl an maximalen gescheiterten Anmeldeversuchen fest, bevor ein Benutzerkonto automatisch deaktiviert wird.",
                "Security:SessionLifeTime" => "Legt die Zeitspanne fest, nach der eine Benutzersitzung abläuft."
            ]
        ],
        "EN" => [
            "Security"    => [
                "AddUser"                => "Add user",
                "ChangeEmail"            => "Change email address",
                "DeleteEntry"            => "Delete entry",
                "DeleteGroup"            => "Delete group",
                "Email"                  => "Email address",
                "Everyone"               => "Everyone",
                "FailedLogins"           => "Failed logins",
                "GroupEditorChangeGroup" => "When changing the group, unsaved changes are lost. Change group?",
                "GroupEditorDeleteGroup" => "Deleting a group loses all permissions and ACL entries. Delete group?",
                "Groups"                 => "Groups",
                "KeepLoggedin"           => "Keep logged in",
                "Memberships"            => "Memberships",
                "NewGroup"               => "New group",
                "NewPassword"            => "New password",
                "NewUser"                => "New user",
                "OldPassword"            => "Old password",
                "Owner"                  => "Owner",
                "Password"               => "Password",
                "Permissions"            => "Permissions",
                "Read"                   => "Read",
                "ResetPassword"          => "Reset password",
                "Status"                 => "Status",
                "User"                   => "User",
                "UserConfiguration"      => "User settings",
                "UserGroup"              => "User/Group",
                "Username"               => "Username",
                "Users"                  => "Users",
                "Visibility"             => "Visibility",
                "Write"                  => "Write",
                "UserCount"              => "Amount of user accounts",
                "GroupCount"             => "Amount of user groups"
            ],
            "Permissions" => [
                "ReadAccessControlList"   => "Determines whether members of the group are allowed to see access control lists",
                "UpdateAccessControlList" => "Determines whether members of the group are allowed to update access control lists",
                "CreateGroup"             => "Determines whether members of the group are allowed to create new user groups",
                "UpdateGroup"             => "Determines whether members of the group are allowed to update user groups",
                "DeleteGroup"             => "Determines whether members of the group are allowed to delete user groups",
                "CreateUser"              => "Determines whether members of the group are allowed to create new user accounts",
                "UpdatePassword"          => "Determines whether members of the group are allowed to edit the password of their own user account",
                "UpdateUser"              => "Determines whether members of the group are allowed to update user accounts",
                "DeleteUser"              => "Determines whether members of the group are allowed to delete user accounts"
            ],
            "Settings"    => [
                "Security:MaxFailedLogins" => "Defines the maximum number of failed login attempts before a user account is automatically deactivated.",
                "Security:SessionLifeTime" => "Defines the time span after which a user session expires."
            ]
        ]
    ];
    
    /**
     * The password of the system User.
     *
     * @var string
     */
    private static string $Password;
    
    /**
     * @inheritDoc
     */
    public static function PreInstall(\Phar $Phar, string $Path): void {
        self::$Password = \readline("System User password: ");
    }
    
    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        
        Expression::Create()
                  ->Database("Security")
                  ->Execute();
        
        //Create tables.
        Expression::Create()
                  ->Table(
                      "Security.Groups",
                      [
                          "ID"                      => ["Type" => Type::BigInt | Type::Unsigned, "Autoincrement" => true],
                          "Name"                    => ["Type" => Type::TinyText, "Collation" => Collation::UTF8],
                          "CreateUser"              => ["Type" => Type::Boolean, "Default" => false],
                          "UpdateUser"              => ["Type" => Type::Boolean, "Default" => false],
                          "DeleteUser"              => ["Type" => Type::Boolean, "Default" => false],
                          "CreateGroup"             => ["Type" => Type::Boolean, "Default" => false],
                          "UpdateGroup"             => ["Type" => Type::Boolean, "Default" => false],
                          "DeleteGroup"             => ["Type" => Type::Boolean, "Default" => false],
                          "ReadAccessControlList"   => ["Type" => Type::Boolean, "Default" => false],
                          "UpdateAccessControlList" => ["Type" => Type::Boolean, "Default" => false]
                      ],
                      [
                          "Primary" => ["Fields" => ["ID"]],
                          "Name"    => ["Unique" => true, "Fields" => ["Name" => 255]]
                      ]
                  )
                  ->Execute();
        Expression::Create()
                  ->Table(
                      "Security.GroupMemberships",
                      [
                          "ID"    => ["Type" => Type::BigInt | Type::Unsigned, "Autoincrement" => true],
                          "Group" => ["Type" => Type::BigInt | Type::Unsigned],
                          "User"  => ["Type" => Type::BigInt | Type::Unsigned]
                      ],
                      [
                          "Primary" => ["Fields" => ["ID", "Group", "User"]]
                      ]
                  )
                  ->Execute();
        Expression::Create()
                  ->Table(
                      "Security.Users",
                      [
                          "ID"               => ["Type" => Type::BigInt | Type::Unsigned, "Autoincrement" => true],
                          "Name"             => ["Type" => Type::TinyText, "Collation" => Collation::UTF8],
                          "Locale"           => ["Type" => Type::Char, "Size" => 2, "Collation" => Collation::ASCII],
                          "Password"         => ["Type" => Type::TinyText, "Collation" => Collation::UTF8],
                          "Email"            => ["Type" => Type::TinyText, "Collation" => Collation::UTF8],
                          "Active"           => ["Type" => Type::Boolean],
                          "FailedLoginCount" => ["Type" => Type::TinyInt | Type::Unsigned, "Size" => 100],
                          "LastLogin"        => ["Type" => Type::Timestamp, "Default" => λ::CurrentTimestamp(), "Update" => λ::CurrentTimestamp()]
                      ],
                      [
                          "Primary" => ["Fields" => ["ID"]],
                          "Name"    => ["Unique" => true, "Fields" => ["Name" => 255, "Email" => 255]]
                      ]
                  )
                  ->Execute();
        Expression::Create()
                  ->Table(
                      "Security.Sessions",
                      [
                          "User"           => ["Type" => Type::BigInt | Type::Unsigned],
                          "Ticket"         => ["Type" => Type::TinyText, "Collation" => Collation::ASCII],
                          "ExpirationTime" => ["Type" => Type::DateTime],
                      ],
                      [
                          "Primary" => ["Fields" => ["User", "Ticket" => 255]]
                      ]
                  )
                  ->Execute();
        Expression::Create()
                  ->Table(
                      "Security.AccessControlLists",
                      [
                          "ID" => ["Type" => Type::BigInt | Type::Unsigned, "Autoincrement" => true],
                      ],
                      [
                          "Primary" => ["Fields" => ["ID"]]
                      ]
                  )
                  ->Execute();
        Expression::Create()
                  ->Table(
                      "Security.AccessControlListEntries",
                      [
                          "ID"                => ["Type" => Type::BigInt | Type::Unsigned, "Autoincrement" => true],
                          "AccessControlList" => ["Type" => Type::BigInt | Type::Unsigned],
                          "Group"             => ["Type" => Type::BigInt | Type::Unsigned, "Nullable" => true, "Default" => null],
                          "User"              => ["Type" => Type::BigInt | Type::Unsigned, "Nullable" => true, "Default" => null],
                          "Read"              => ["Type" => Type::Boolean],
                          "Write"             => ["Type" => Type::Boolean],
                          "Delete"            => ["Type" => Type::Boolean]
                      ],
                      [
                          "Primary"     => ["Fields" => ["ID", "AccessControlList"]],
                          "Permissions" => ["Unique" => true, "Fields" => ["Group", "User", "Read", "Write", "Delete"]]
                      ]
                  )
                  ->Execute();
        
        //Install Module.
        /** @var \Modules\Security $Security */
        $Security = \vDesk\Modules::Security();
        $Security->Commands->Add(
            new Command(
                null,
                $Security,
                "Login",
                false,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "User", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "Password", \vDesk\Struct\Type::String, false, false)
                ])
            )
        );
        $Security->Commands->Add(
            new Command(
                null,
                $Security,
                "ReLogin",
                true,
                false,
                null
            )
        );
        $Security->Commands->Add(
            new Command(
                null,
                $Security,
                "Logout",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "User", \vDesk\Struct\Type::String, false, false)])
            )
        );
        $Security->Commands->Add(
            new Command(
                null,
                $Security,
                "GetUsers",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "View", \vDesk\Struct\Type::Bool, false, false)])
            )
        );
        $Security->Commands->Add(
            new Command(
                null,
                $Security,
                "CreateUser",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "Name", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "Password", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "Email", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "Active", \vDesk\Struct\Type::Bool, false, false),
                    new Parameter(null, null, "Locale", \vDesk\Struct\Type::String, false, false)
                ])
            )
        );
        $Security->Commands->Add(
            new Command(
                null,
                $Security,
                "UpdateUser",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Name", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "Email", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "Active", \vDesk\Struct\Type::Bool, false, false),
                    new Parameter(null, null, "FailedLoginCount", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Locale", \vDesk\Struct\Type::String, false, false)
                ])
            )
        );
        $Security->Commands->Add(
            new Command(
                null,
                $Security,
                "SetMemberships",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Add", \vDesk\Struct\Type::Array, false, false),
                    new Parameter(null, null, "Delete", \vDesk\Struct\Type::Array, false, false)
                ])
            )
        );
        $Security->Commands->Add(
            new Command(
                null,
                $Security,
                "ResetPassword",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "Old", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "New", \vDesk\Struct\Type::String, false, false)
                ])
            )
        );
        $Security->Commands->Add(
            new Command(
                null,
                $Security,
                "UpdateEmail",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "Email", \vDesk\Struct\Type::String, false, false)])
            )
        );
        $Security->Commands->Add(
            new Command(
                null,
                $Security,
                "UpdateLocale",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "Locale", \vDesk\Struct\Type::String, false, false)])
            )
        );
        $Security->Commands->Add(
            new Command(
                null,
                $Security,
                "DeleteUser",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false)])
            )
        );
        $Security->Commands->Add(
            new Command(
                null,
                $Security,
                "GetGroups",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "View", \vDesk\Struct\Type::Bool, false, false)])
            )
        );
        $Security->Commands->Add(
            new Command(
                null,
                $Security,
                "CreateGroup",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "Name", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "Permissions", \vDesk\Struct\Type::Object, false, false)
                ])
            )
        );
        $Security->Commands->Add(
            new Command(
                null,
                $Security,
                "UpdateGroup",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Name", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "Permissions", \vDesk\Struct\Type::Object, false, false)
                ])
            )
        );
        $Security->Commands->Add(
            new Command(
                null,
                $Security,
                "CreatePermission",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "Name", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "Default", \vDesk\Struct\Type::Bool, false, true)
                ])
            )
        );
        $Security->Commands->Add(
            new Command(
                null,
                $Security,
                "DeletePermission",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "Name", \vDesk\Struct\Type::String, false, false)])
            )
        );
        $Security->Commands->Add(
            new Command(
                null,
                $Security,
                "DeleteGroup",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false)])
            )
        );
        $Security->Commands->Add(
            new Command(
                null,
                $Security,
                "GetAccessControlList",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false)])
            )
        );
        $Security->Commands->Add(
            new Command(
                null,
                $Security,
                "UpdateAccessControlList",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Add", \vDesk\Struct\Type::Array, false, false),
                    new Parameter(null, null, "Update", \vDesk\Struct\Type::Array, false, false),
                    new Parameter(null, null, "Delete", \vDesk\Struct\Type::Array, false, false),
                ])
            )
        );
        
        $Security->Save();
        
        //Create default Groups.
        $Everyone = new Group(
            null,
            "Everyone",
            new Group\Permissions([
                "ReadAccessControlList"   => true,
                "UpdateAccessControlList" => true
            ])
        );
        $Everyone->Save();
        
        $Administration = new Group(
            null,
            "Administration",
            new Group\Permissions([
                "ReadAccessControlList"   => true,
                "UpdateAccessControlList" => true,
                "CreateUser"              => true,
                "UpdateUser"              => true,
                "DeleteUser"              => true,
                "CreateGroup"             => true,
                "UpdateGroup"             => true,
                "DeleteGroup"             => true
            ])
        );
        $Administration->Save();
        
        //Create system user.
        $User = new User(
            null,
            "System",
            "EN",
            \password_hash(self::$Password, \PASSWORD_DEFAULT),
            "",
            true,
            0,
            new User\Groups([$Everyone, $Administration])
        );
        $User->Save();
        \vDesk::$User = $User;
        
        Settings::$Remote["Security"] = new Settings\Remote\Settings(
            [
                "MaxFailedLogins" => new Settings\Remote\Setting("MaxFailedLogins", 10, \vDesk\Struct\Type::Int),
                "SessionLifeTime" => new Settings\Remote\Setting("SessionLifeTime", "03:00:00", \vDesk\Struct\Extension\Type::TimeSpan)
            ],
            "Security"
        );
        
        //Extract files.
        self::Deploy($Phar, $Path);
        
    }
    
    /**
     * @inheritDoc
     */
    public static function Uninstall(string $Path): void {
        
        //Uninstall Module.
        /** @var \Modules\Security $Security */
        $Security = \vDesk\Modules::Security();
        $Security->Delete();
        
        //Drop database.
        Expression::Drop()
                  ->Database("Security")
                  ->Execute();
        
        //Delete files.
        self::Undeploy();
        
    }
}