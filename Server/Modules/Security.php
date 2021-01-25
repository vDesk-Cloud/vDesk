<?php
declare(strict_types=1);

namespace Modules;

use vDesk\Configuration\Settings;
use vDesk\DataProvider\Expression;
use vDesk\DataProvider\Expression\Functions;
use vDesk\DataProvider\Expression\λ;
use vDesk\DataProvider\Type;
use vDesk\Modules\Command;
use vDesk\Modules\Module;
use vDesk\Security\AccessControlList;
use vDesk\Security\AccessControlList\Entry;
use vDesk\Security\Groups;
use vDesk\Security\GroupsView;
use vDesk\Security\Users;
use vDesk\Security\UsersView;
use vDesk\Security\Group;
use vDesk\Security\TicketExpiredException;
use vDesk\Security\UnauthorizedAccessException;
use vDesk\Security\User;
use vDesk\Utils\Log;
use vDesk\Utils\Validate;
use vDesk\Struct\Extension;

/**
 * The central security Module of vDesk.
 * Manages access to objects, administrates Users and Groups.
 *
 * @package vDesk\Security
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Security extends Module {
    
    /**
     * Performs a login and creates a session ticket.
     *
     * @param string|null $User     The name or email-address of the User.
     * @param null|string $Password The password of the User.
     *
     * @return \vDesk\Security\User The logged in User.
     * @throws \vDesk\Security\UnauthorizedAccessException
     */
    public static function Login(string $User = null, string $Password = null): User {
        $User ??= Command::$Parameters["User"];
        
        $Expression = Expression::Select("*")
                                ->From("Security.Users");
        
        if(Validate::As($User, Extension\Type::Email)) {
            $Expression->Where(["Email" => $User]);
        } else {
            $Expression->Where(["Name" => $User]);
        }
        
        $Result = $Expression->Execute();
        
        //Check if the User exists.
        if($Result->Count === 0) {
            Log::Warn(__METHOD__, "Login failed for User with name or email '{$User}' => User doesn't exist!");
            throw new UnauthorizedAccessException("Name/email and/or password incorrect!");
        }
        
        $Row = $Result->ToMap();
        
        //Check if the User is active.
        if(!(bool)$Row["Active"]) {
            Log::Warn(__METHOD__, "Login failed for User with name '{$User}' => Account currently disabled!");
            throw new UnauthorizedAccessException("Account currently disabled!");
        }
    
        $User = new User(
            (int)$Row["ID"],
            $Row["Name"],
            $Row["Locale"],
            null,
            $Row["Email"],
            (bool)$Row["Active"],
            (int)$Row["FailedLoginCount"]
        );
        
        //Check if the password is correct.
        //@todo Consider hashing the password once on the client and a second time on the server to prevent MITM attacks?
        if(!\password_verify($Password ?? Command::$Parameters["Password"], $Row["Password"])) {
            $User->FailedLoginCount++;
            //Check if the User reached the maximum amount of failed login attempts.
            if($User->FailedLoginCount >= Settings::$Remote["Security"]["MaxFailedLogins"]) {
                $User->Active = false;
            }
            $User->Save();
            Log::Warn(__METHOD__, "Login failed for User with name '{$User->Name}' => Password incorrect!");
            throw new UnauthorizedAccessException("Username and/or password incorrect!");
        }
        
        $User->FailedLoginCount = (int)$Row["FailedLoginCount"];
        
        $User->Memberships = new User\Groups([], $User);
        $User->Permissions->Fill();
        $User->Ticket = \uniqid("", true);
        
        //Delete any old sessions.
        Expression::Delete()
                  ->From("Security.Sessions")
                  ->Where(["User" => $User])
                  ->Execute();
        
        //Create new session.
        Expression::Insert()
                  ->Into("Security.Sessions")
                  ->Values([
                      "User"           => $User,
                      "Ticket"         => $User->Ticket,
                      "ExpirationTime" => λ::AddTime(
                          λ::Now(),
                          Settings::$Remote["Security"]["SessionLifeTime"]
                      )
                  ])();
        
        Log::Info(__METHOD__, "User '{$User->Name}' logged in.");
        \vDesk::$User = $User;
        return $User;
    }
    
    /**
     * Performs a login with a valid session ticket.
     *
     * @param null|string $Ticket The ticket of the user to login.
     *
     * @return \vDesk\Security\User The logged in User.
     * @throws \vDesk\Security\UnauthorizedAccessException
     */
    public static function ReLogin(string $Ticket = null): User {
        $User = User::FromTicket($Ticket ?? Command::$Ticket);
        if(!$User->Active) {
            Log::Warn(__METHOD__, "Re-login failed for User with name '{$User->Name}' => Account currently disabled.");
            throw new UnauthorizedAccessException();
        }
        $User->Permissions->Fill();
        return $User;
    }
    
    /**
     * Logs the current user out.
     *
     * @return bool True if the current User successfully logged out.
     */
    public static function Logout(): bool {
        Expression::Delete()
                  ->From("Security.Sessions")
                  ->Where(["User" => \vDesk::$User])
                  ->Execute();
        Log::Info(__METHOD__, "User " . \vDesk::$User->Name . " logged out.");
        return true;
    }
    
    /**
     * Determines whether a ticket is valid.
     *
     * @param string|null $Ticket
     *
     * @throws \vDesk\Security\TicketExpiredException Thrown if the ticket exceeded its lifetime.
     */
    public static function ValidateTicket(string $Ticket = null): void {
        $Ticket ??= Command::$Ticket;
        
        $Result = Expression::Select("ExpirationTime")
                            ->From("Security.Sessions")
                            ->Where([
                                "Ticket"         => $Ticket,
                                "ExpirationTime" => [">" => λ::Now()]
                            ])
                            ->Execute();
        
        if($Result->Count === 0) {
            throw new TicketExpiredException();
        }
        
        Expression::Update("Security.Sessions")
                  ->Set([
                      "ExpirationTime" => λ::AddTime(
                          λ::Now(),
                          Settings::$Remote["Security"]["SessionLifeTime"]
                      )
                  ])
                  ->Where(["Ticket" => $Ticket])
                  ->Execute();
        
        \vDesk::$User = User::FromTicket($Ticket);
    }
    
    /**
     * Gets all existing Users.
     *
     * @param bool $View Flag indicating whether the method should return an view of users.
     *                   If set to true, this method returns a {@link \vDesk\Security\UsersView} instead of a full
     *                   {@link \vDesk\Security\Users}.
     *
     * @return \vDesk\Security\Users A Collection of all existing Users.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to update Users.
     */
    public static function GetUsers(bool $View = null): Users {
        if($View ?? Command::$Parameters["View"]) {
            return UsersView::All();
        }
        if(!\vDesk::$User->Permissions["UpdateUser"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to view Users without having permissions.");
            throw new UnauthorizedAccessException();
        }
        return Users::All();
    }
    
    /**
     * Creates a new User.
     *
     * @param string|null $Name     The name of the User.
     * @param string|null $Locale   The locale of the User.
     * @param string|null $Password The initial password of the User.
     * @param string|null $Email    The email address of the User.
     * @param bool|null   $Active   Flag indicating whether the User is active.
     *
     * @return \vDesk\Security\User The newly created user.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to create new Users.
     */
    public static function CreateUser(string $Name = null, string $Locale = null, string $Password = null, string $Email = null, bool $Active = null): User {
        if(!\vDesk::$User->Permissions["CreateUser"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to create a new User without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $User = new User(
            null,
            $Name ?? Command::$Parameters["Name"],
            $Locale ?? Command::$Parameters["Locale"],
            \password_hash($Password ?? Command::$Parameters["Password"], \PASSWORD_DEFAULT),
            $Email ?? Command::$Parameters["Email"],
            $Active ?? Command::$Parameters["Active"],
            0,
            new User\Groups([new Group(Group::Everyone)])
        );
        $User->Save();
        (new User\Added($User))->Dispatch();
        return $User;
    }
    
    /**
     * Updates an User.
     *
     * @param int|null    $ID               The ID of the User.
     * @param string|null $Name             The new name of the User.
     * @param string|null $Locale           The new locale of the User.
     * @param string|null $Password         The new password of the User.
     * @param string|null $Email            The new email address of the User.
     * @param bool|null   $Active           Flag indicating whether the User is active.
     * @param int|null    $FailedLoginCount The new amount of failed login attempts of the User.
     *
     * @return \vDesk\Security\User The updated User.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to update Users.
     */
    public static function UpdateUser(
        int $ID = null,
        string $Name = null,
        string $Locale = null,
        string $Password = null,
        string $Email = null,
        bool $Active = null,
        int $FailedLoginCount = null
    ): User {
        if(!\vDesk::$User->Permissions["UpdateUser"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to update an User without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $ID                     ??= Command::$Parameters["ID"];
        $Password               ??= Command::$Parameters["Password"] ?? null;
        $User                   = (new User($ID))->Fill();
        $User->Name             = $Name ?? Command::$Parameters["Name"];
        $User->Locale           = $Locale ?? Command::$Parameters["Locale"];
        $User->Email            = $Email ?? Command::$Parameters["Email"];
        $User->Active           = $Active ?? Command::$Parameters["Active"];
        $User->FailedLoginCount = $FailedLoginCount ?? Command::$Parameters["FailedLoginCount"];
        $User->Save();
        if($Password !== null) {
            Expression::Update("Security.Users")
                      ->Set(["Password" => $Password])
                      ->Where(["ID" => $ID])
                      ->Execute();
        }
        (new User\Updated($User))->Dispatch();
        return $User;
    }
    
    /**
     * Sets the memberships of an User to a specified set of Groups.
     *
     * @param int|null   $ID     The ID of the User to update.
     * @param int[]|null $Add    The IDs of the Groups to add.
     * @param int[]|null $Delete The IDs of the Groups to delete.
     *
     * @return \vDesk\Security\User The updated User.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to update Users.
     * @todo Rename to "UpdateGroups"?
     */
    public static function SetMemberships(int $ID = null, array $Add = null, array $Delete = null): User {
        if(!\vDesk::$User->Permissions["UpdateUser"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to set Group memberships without having permissions.");
            throw new UnauthorizedAccessException();
        }
        
        $User = (new User($ID ?? Command::$Parameters["ID"]))->Fill();
        
        //Add new Groups.
        foreach($Add ?? Command::$Parameters["Add"] as $Added) {
            $User->Memberships->Add(new Group($Added));
        }
        
        //Delete removed Groups.
        foreach($Delete ?? Command::$Parameters["Delete"] as $Deleted) {
            if(($Group = $User->Memberships->Find(static fn(Group $Group): bool => $Group->ID === $Deleted)) !== null) {
                $User->Memberships->Remove($Group);
            }
        }
        
        $User->Memberships->Save();
        return $User;
    }
    
    /**
     * Updates the password of the current User.
     * Performs a login with the old credentials, so the current ticket will be rendered invalid.
     *
     * @param null|string $Old The current password of the User.
     * @param null|string $New The new password to set.
     *
     * @return \vDesk\Security\User The updated User.
     */
    public static function ResetPassword(string $Old = null, string $New = null): User {
        $User = self::Login(\vDesk::$User->Name, $Old ?? Command::$Parameters["Old"]);
        Expression::Update("Security.Users")
                  ->Set(["Password" => \password_hash($New ?? Command::$Parameters["New"], \PASSWORD_DEFAULT)])
                  ->Where(["ID" => $User->ID])
                  ->Execute();
        return $User;
    }
    
    /**
     * Updates the email address of the current User.
     *
     * @param null|string $Email The new email to set.
     *
     * @return \vDesk\Security\User The updated User.
     */
    public static function UpdateEmail(string $Email = null): User {
        \vDesk::$User->Email = $Email ?? Command::$Parameters["Email"];
        \vDesk::$User->Save();
        return \vDesk::$User;
    }
    
    /**
     * Updates the locale of the current User.
     *
     * @param null|string $Locale The new locale to set.
     *
     * @return \vDesk\Security\User The updated User.
     */
    public static function UpdateLocale(string $Locale = null): User {
        \vDesk::$User->Locale = $Locale ?? Command::$Parameters["Locale"];
        \vDesk::$User->Save();
        return \vDesk::$User;
    }
    
    /**
     * Deletes an {@link \vDesk\Security\User}-account and all of its group-memberships from the system.
     *
     * @param null|int $ID The ID of the User to delete.
     *
     * @return bool True if the User has been successfully deleted.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to delete Users.
     */
    public static function DeleteUser(int $ID = null): bool {
        if(!\vDesk::$User->Permissions["DeleteUser"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to delete an User without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $User = new User($ID ?? Command::$Parameters["ID"]);
        if($User->ID === User::System) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to delete the system User.");
            throw new UnauthorizedAccessException();
        }
        $User->Delete();
        (new User\Deleted($User))->Dispatch();
        return true;
    }
    
    /**
     * Gets all groups of the system.
     *
     * @param bool $View Flag indicating whether the method should return an view of groups.
     *                   If set to true, this method returns an {@link \vDesk\Security\GroupsView} instead of a full
     *                   {@link \vDesk\Security\Groups}.
     *
     * @return \vDesk\Security\Groups A Collection of all existing Groups.
     * @throws \vDesk\Security\UnauthorizedAccessException  Thrown if the current User doesn't have permissions to update Groups.
     *
     */
    public static function GetGroups(bool $View = null): Groups {
        if($View ?? Command::$Parameters["View"]) {
            return GroupsView::FetchAll();
        }
        if(!\vDesk::$User->Permissions["UpdateGroup"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to view Groups without having permissions.");
            throw new UnauthorizedAccessException();
        }
        return Groups::FetchAll();
    }
    
    /**
     * Creates a new Group.
     *
     * @param null|string $Name        The name of the Group.
     * @param bool[]      $Permissions The permissions of the Group.
     *
     * @return \vDesk\Security\Group The newly created Group.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to create new Groups.
     */
    public static function CreateGroup(string $Name = null, array $Permissions = null): Group {
        if(!\vDesk::$User->Permissions["CreateGroup"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to create a new Group without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Group = new Group(
            null,
            $Name ?? Command::$Parameters["Name"],
            new Group\Permissions($Permissions ?? Command::$Parameters["Permissions"])
        );
        $Group->Save();
        (new Group\Added($Group))->Dispatch();
        return $Group;
    }
    
    /**
     * Updates a Group.
     *
     * @param null|int    $ID          The ID of the Group to update.
     * @param null|string $Name        The new name of the Group.
     * @param bool[]      $Permissions The new permissions of the Group.
     *
     * @return \vDesk\Security\Group The updated Group.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to update Groups.
     */
    public static function UpdateGroup(int $ID = null, string $Name = null, array $Permissions = null): Group {
        if(!\vDesk::$User->Permissions["UpdateGroup"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to update a Group without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Group       = (new Group($ID ?? Command::$Parameters["ID"]))->Fill();
        $Group->Name = $Name ?? Command::$Parameters["Name"];
        foreach($Permissions ?? Command::$Parameters["Permissions"] as $Permission => $Value) {
            $Group->Permissions[$Permission] = $Value;
        }
        $Group->Save();
        return $Group;
    }
    
    /**
     * Creates a new Group Permission.
     *
     * @param string|null $Name    The name of the Permission to create.
     * @param bool|null   $Default The default value of the Permission.
     *
     * @return bool True if the Permission has been successfully created.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to update Groups.
     */
    public static function CreatePermission(string $Name = null, bool $Default = null): bool {
        if(!\vDesk::$User->Permissions["UpdateGroup"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to create a Group Permission without having permissions.");
            throw new UnauthorizedAccessException();
        }
        Expression::Alter()
                  ->Table("Security.Groups")
                  ->Add([$Name ?? Command::$Parameters["Name"] => ["Type" => Type::Boolean, "Default" => $Default ?? Command::$Parameters["Default"] ?? false]])
                  ->Execute();
        return true;
    }
    
    /**
     * Deletes a Group Permission.
     *
     * @param string|null $Name The name of the Permission to delete.
     *
     * @return bool True if the Permission has been successfully deleted.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to update Groups.
     */
    public static function DeletePermission(string $Name = null): bool {
        if(!\vDesk::$User->Permissions["UpdateGroup"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to create a Group Permission without having permissions.");
            throw new UnauthorizedAccessException();
        }
        Expression::Alter()
                  ->Table("Security.Groups")
                  ->Drop([$Name])
                  ->Execute();
        return true;
    }
    
    /**
     * Deletes a Group.
     *
     * @param int|null $ID The ID of the Group to delete.
     *
     * @return bool True if the Group has been successfully deleted.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to delete Groups.
     */
    public static function DeleteGroup(int $ID = null): bool {
        if(!\vDesk::$User->Permissions["DeleteGroup"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to delete a Group without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Group = new Group($ID ?? Command::$Parameters["ID"]);
        $Group->Delete();
        (new Group\Deleted($Group))->Dispatch();
        return true;
    }
    
    /**
     * Gets an AccessControlList.
     *
     * @param int|null $ID The ID of the AccessControlList to get.
     *
     * @return \vDesk\Security\AccessControlList The requested AccessControlList.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to read AccessControlLists or doesn't have
     *                                                     read permissions on the AccessControlList to get.
     */
    public static function GetAccessControlList(int $ID = null): AccessControlList {
        $AccessControlList = (new AccessControlList([], $ID ?? Command::$Parameters["ID"]))->Fill(\vDesk::$User);
        if(!\vDesk::$User->Permissions["ReadAccessControlList"] || !$AccessControlList->Read) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to view AccessControlList without having permissions.");
            throw new UnauthorizedAccessException();
        }
        return $AccessControlList;
    }
    
    /**
     * Updates an AccessControlList.
     *
     * @param int|null      $ID     The ID of the AccessControlList to update.
     *
     * @param object[]|null $Add    The Entries to add.
     * @param object[]|null $Update The Entries to update.
     * @param int[]|null    $Delete The IDs of the Entries to delete.
     *
     * @return \vDesk\Security\AccessControlList The updated AccessControlList.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to update AccessControlLists or doesn't have
     *                                                     write permissions on the AccessControlList to update.
     */
    public static function UpdateAccessControlList(int $ID = null, array $Add = null, array $Update = null, array $Delete = null): AccessControlList {
        $AccessControlList = (new AccessControlList([], $ID ?? Command::$Parameters["ID"]))->Fill(\vDesk::$User);
        if(!\vDesk::$User->Permissions["UpdateAccessControlList"] || !$AccessControlList->Write) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to update AccessControlList with without having permissions.");
            throw new UnauthorizedAccessException();
        }
        
        //Add new Entries.
        foreach($Add ?? Command::$Parameters["Add"] as $Added) {
            $AccessControlList->Add(
                new Entry(
                    null,
                    new Group($Added["Group"]),
                    new User($Added["User"]),
                    $Added["Read"],
                    $Added["Write"],
                    $Added["Delete"]
                )
            );
        }
        
        //Update changed entries.
        foreach($Update ?? Command::$Parameters["Update"] as $Updated) {
            if(($Entry = $AccessControlList->Find(static fn(Entry $Entry): bool => $Entry->ID === $Updated["ID"])) !== null) {
                $Entry->Read   = $Updated["Read"];
                $Entry->Write  = $Updated["Write"];
                $Entry->Delete = $Updated["Delete"];
            }
        }
        
        //Delete removed entries.
        foreach($Delete ?? Command::$Parameters["Delete"] as $Deleted) {
            if(($Entry = $AccessControlList->Find(static fn(Entry $Entry): bool => $Entry->ID === $Deleted)) !== null) {
                $AccessControlList->Remove($Entry);
            }
        }
        
        $AccessControlList->Save();
        return $AccessControlList;
    }
    
    /**
     * Gets the status information of the PinBoard.
     *
     * @return null|array An array containing the amount of PinBoard Notes and attached Elements.
     */
    public static function Status(): ?array {
        return [
            "UserCount"  => Expression::Select(Functions::Count("*"))
                                      ->From("Security.Users")(),
            "GroupCount" => Expression::Select(Functions::Count("*"))
                                      ->From("Security.Groups")()
        ];
    }
    
}
