<?php
declare(strict_types=1);

namespace vDesk\Security;

/**
 * Represents a view on a collection of {@link \vDesk\Security\User} objects.
 *
 * @package Desk\Security
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class UsersView extends Users {

    /**
     * Returns a JSON-encodable representation of the UsersView.
     *
     * @return array An array of shallow copies of the Users of the UsersView.
     */
    public function ToDataView(bool $Protected = true): array {
        return $this->Reduce(static function(array $Users, User $User): array {
            $Users[] = ["ID" => $User->ID, "Name" => $User->Name];
            return $Users;
        }, []);
    }

}
