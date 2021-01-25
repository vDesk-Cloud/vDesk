<?php

use vDesk\DataProvider\Expression;
use vDesk\Events\EventListener;
use vDesk\Security\User;
use vDesk\Security\User\Deleted;

/**
 * Eventlistener that listens on the 'vDesk.Security.User.Deleted'-event and deletes all Notes and Attachments of the User from the PinBoard.
 */
return new EventListener(
    Deleted::Name,
    static function(User $User) {
        Expression::Delete()
                  ->From("PinBoard.Attachments")
                  ->Where(["Owner" => $User])
                  ->Execute();
        Expression::Delete()
                  ->From("PinBoard.Notes")
                  ->Where(["Owner" => $User])
                  ->Execute();
    }
);
