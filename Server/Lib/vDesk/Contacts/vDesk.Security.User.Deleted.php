<?php

use vDesk\DataProvider\Expression;
use vDesk\Events\EventListener;
use vDesk\Security\User;
use vDesk\Security\User\Deleted;

/**
 * Eventlistener that listens on the 'vDesk.Security.User.Deleted' event.
 */
return new EventListener(
    Deleted::Name,
    static function(User $User) {
        Expression::Update("Contacts.Contacts")
                  ->Set(["Owner" => User::System])
                  ->Where(["Owner" => $User])
                  ->Execute();
    }
);
