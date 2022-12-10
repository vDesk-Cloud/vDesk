<?php
declare(strict_types=1);

namespace vDesk\Security\User;

use vDesk\DataProvider\Expression;
use vDesk\Data\IDataView;
use vDesk\Security\User;
use vDesk\Struct\Collections\Typed;

/**
 * Class that represents a typed Collection of Users containing utility functions for loading Users from the database.
 *
 * @package vDesk\Security
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Collection extends Typed\Collection implements IDataView {

    /**
     * The Type of the User Collection.
     */
    public const Type = User::class;

    /**
     * Creates a new User Collection containing every existing User.
     *
     * @return \vDesk\Security\User\Collection A collection containing every existing User.
     */
    public static function All(): static {
        return new static(
            (static function() {
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
                    $User              = new User(
                        (int)$Row["ID"],
                        $Row["Name"],
                        $Row["Locale"],
                        null,
                        $Row["Email"],
                        (bool)$Row["Active"],
                        (int)$Row["FailedLoginCount"]
                    );
                    $User->Memberships = Groups::FromUser($User);
                    yield $User;
                }
            })()
        );
    }

    /** @inheritdoc */
    public function ToDataView(bool $Reference = false): array {
        return $this->Reduce(static function(array $Users, User $User) use ($Reference): array {
            $Users[] = $User->ToDataView($Reference);
            return $Users;
        },
            []);
    }

    /** @inheritdoc */
    public static function FromDataView($DataView = []): IDataView {
        return new static(
            (static function() use ($DataView) {
                foreach($DataView as $User) {
                    yield User::FromDataView($User);
                }
            })()
        );
    }

    /** @inheritdoc */
    public function Find(callable $Predicate): ?User {
        return parent::Find($Predicate);
    }

    /** @inheritdoc */
    public function Remove($Element): User {
        return parent::Remove($Element);
    }

    /** @inheritdoc */
    public function RemoveAt(int $Index): User {
        return parent::RemoveAt($Index);
    }

    /** @inheritdoc */
    public function offsetGet($Index): User {
        return parent::offsetGet($Index);
    }

}
