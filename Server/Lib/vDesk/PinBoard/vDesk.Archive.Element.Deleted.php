<?php

use vDesk\DataProvider\Expression;
use vDesk\Security\User\Deleted;

/**
 * Event listener that listens on the 'vDesk.Security.User.Deleted'-event
 * and deletes all Notes and Attachments of the User from the PinBoard.
 */
return [
    Deleted::Name => static function($Arguments) {
        Expression::Delete()
                  ->From("PinBoard.Attachments")
                  ->Where(["Owner" => $Arguments->ID])
                  ->Execute();
        Expression::Delete()
                  ->From("PinBoard.Notes")
                  ->Where(["Owner" => $Arguments->ID])
                  ->Execute();
    }
];