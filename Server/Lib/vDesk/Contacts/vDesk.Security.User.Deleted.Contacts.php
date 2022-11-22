<?php

use vDesk\DataProvider\Expression;
use vDesk\Security\User;
use vDesk\Security\User\Deleted;

/**
 * Event listener that listens on the 'vDesk.Security.User.Deleted' event.
 * and sets the ownership of all affected Contacts to the system User.
 */
return [
    Deleted::Name,
    static function(Deleted $Event) {
        Expression::Update("Contacts.Contacts")
                  ->Set(["Owner" => User::System])
                  ->Where(["Owner" => $Event->User])
                  ->Execute();
    }
];
