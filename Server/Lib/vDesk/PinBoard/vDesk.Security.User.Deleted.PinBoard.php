<?php

use vDesk\DataProvider\Expression;
use vDesk\Security\User\Deleted;

/**
 * Event listener that listens on the 'vDesk.Security.User.Deleted'-event
 * and deletes all Notes and Attachments of the User from the PinBoard.
 */
return [
    Deleted::Name,
    static function(Deleted $Event) {
        Expression::Delete()
                  ->From("PinBoard.Attachments")
                  ->Where(["Owner" => $Event->User])
                  ->Execute();
        Expression::Delete()
                  ->From("PinBoard.Notes")
                  ->Where(["Owner" => $Event->User])
                  ->Execute();
    }
];
