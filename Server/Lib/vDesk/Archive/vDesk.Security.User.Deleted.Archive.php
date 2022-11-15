<?php

use vDesk\DataProvider\Expression;
use vDesk\Security\User;
use vDesk\Security\User\Deleted;

/**
 * Event listener that listens on the 'vDesk.Security.User.Deleted' Event
 * and sets the ownership of all affected Elements to the system User.
 */
return [
    Deleted::Name,
    static function(User $User) {
        Expression::Update("Archive.Elements")
                  ->Set(["Owner" => User::System])
                  ->Where(["Owner" => $User])
                  ->Execute();
    }
];
