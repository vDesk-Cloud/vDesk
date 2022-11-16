<?php

use vDesk\DataProvider\Expression;
use vDesk\Security\User\Deleted;

/**
 * Event listener that listens on the 'vDesk.Security.User.Deleted' event.
 * and deletes all private and Group Messages of the deleted User.
 */
return [
    Deleted::Name,
    static function(Deleted $Event) {
        Expression::Delete()
                  ->From("Messenger.Messages")
                  ->Where(["Sender" => $Event->User])
                  ->Execute();
        Expression::Delete()
                  ->From("Messenger.GroupMessages")
                  ->Where(["Sender" => $Event->User])
                  ->Execute();
    }
];
