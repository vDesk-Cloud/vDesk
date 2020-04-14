<?php

use vDesk\DataProvider\Expression;
use vDesk\Events\EventListener;
use vDesk\Security\User\Deleted;

/**
 * Eventlistener that listens on the 'vDesk.Security.User.Deleted'-event and deletes all Notes and Attachments of the User from the PinBoard.
 */
return new EventListener(
    Deleted::Name,
    static function($Arguments) {
        Expression::Delete()
                  ->From("PinBoard.Attachments")
                  ->Where(["Owner" => $Arguments->ID])
                  ->Execute();
        Expression::Delete()
                  ->From("PinBoard.Notes")
                  ->Where(["Owner" => $Arguments->ID])
                  ->Execute();
    }
);
