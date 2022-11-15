<?php

use vDesk\DataProvider\Expression;
use vDesk\Security\User;
use vDesk\Security\User\Deleted;

/**
 * Event listener that listens on the 'vDesk.Security.User.Deleted' event.
 * and deletes all private and Group Messages of the deleted User.
 */
return [
    Deleted::Name,
    static function(User $User) {
        Expression::Delete()
                  ->From("Messenger.Messages")
                  ->Where(["Sender" => $User])
                  ->Execute();
        Expression::Delete()
                  ->From("Messenger.GroupMessages")
                  ->Where(["Sender" => $User])
                  ->Execute();
    }
];
