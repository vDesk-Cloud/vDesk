<?php

use vDesk\DataProvider\Expression;
use vDesk\Archive\Element\Deleted;

/**
 * Event listener that listens on the 'vDesk.Security.User.Deleted'-event
 * and deletes all Attachments of the Element from the PinBoard.
 */
return [
    Deleted::Name,
    static function(Deleted $Event) {
        Expression::Delete()
                  ->From("PinBoard.Attachments")
                  ->Where(["Element" => $Event->Element])
                  ->Execute();
    }
];