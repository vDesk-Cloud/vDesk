<?php
declare(strict_types=1);

namespace vDesk\Security;

use vDesk\DataProvider\Expression;
use vDesk\Data\IDataView;
use vDesk\Struct\Collections\Typed\Collection;

/**
 * Represents a collection of {@link \vDesk\Security\User} objects.
 *
 * @package vDesk\Security
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class Users extends Collection implements IDataView {

    /**
     * The Type of the Groups.
     */
    public const Type = User::class;

    /**
     * Initializes a new instance of the Users class.
     *
     * @param iterable|null $Elements
     * @param bool          $AutoFill Determines whether the Users should be filled by creation.
     */
    public function __construct(?iterable $Elements = [], bool $AutoFill = false) {

        parent::__construct($Elements);

        if($AutoFill) {
            $this->Fill();
        }
    }

    /**
     * Fetches a collection containing every existing {@link \vDesk\Security\User} user.
     *
     * @return \vDesk\Security\Users A collection containing every existing User.
     */
    public static function All(): Users {
        return new static(static::FetchUsers());
    }

    /**
     * Fetches a generator that yields every existing {@link \vDesk\Security\User} user.
     *
     * @return \Generator A generator that yields every existing {@link \vDesk\Security\User} user.
     */
    protected static function FetchUsers(): \Generator {
        //Hydrate.
        foreach(
            Expression::Select(
                "ID",
                "Name",
                "Locale",
                "Email",
                "Active",
                "FailedLoginCount"
            )
                      ->From("Security.Users")
                      ->OrderBy(["ID" => true])
            as $Row
        ) {
            $User                   = new User((int)$Row["ID"]);
            $User->Name             = $Row["Name"];
            $User->Locale           = $Row["Locale"];
            $User->Email            = $Row["Email"];
            $User->Active           = (bool)$Row["Active"];
            $User->FailedLoginCount = (int)$Row["FailedLoginCount"];
            $User->Memberships      = User\Groups::FromUser($User);
            yield $User;
        }
    }

    /**
     * Returns a JSON-encodable representation of the Users.
     *
     * @param bool $Protected
     *
     * @return array
     */
    public function ToDataView(bool $Protected = true): array {
        return $this->Reduce(static function(array $Users, User $User): array {
            $Users[] = $User->ToDataView();
            return $Users;
        }, []);
    }

    /**
     * Creates an Groups from a JSON-encodable representation.
     *
     * @param mixed $DataView The Data to use to create an instance of the Groups. The type and format should match the output
     *                        of @return \vDesk\Security\Users An instance of the implementing class filled with the provided
     *                        data.
     *
     * @see \vDesk\Security\Groups::ToDataView().
     *
     */
    public static function FromDataView($DataView = []): IDataView {
        return new static(
            (static function() use ($DataView) {
                foreach($DataView as $aoData) {
                    yield User::FromDataView($aoData);
                }
            })()
        );
    }

    /**
     * Fills the Users with all existing users.
     */
    public function Fill(): void {
        foreach(static::FetchUsers() as $User) {
            $this->Add($User);
        }
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

}
