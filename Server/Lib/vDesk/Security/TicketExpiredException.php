<?php
declare(strict_types=1);

namespace vDesk\Security;

/**
 * Exception that is thrown when a ticket is expired.
 *
 * @package vDesk\Security
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class TicketExpiredException extends \Exception {
    
    /**
     * Initializes a new instance of the TicketExpiredException class.
     *
     * @param string          $message  Initializes the TicketExpiredException with the specified message.
     * @param int             $code     Initializes the TicketExpiredException with the specified code.
     * @param \Throwable|null $previous Initializes the TicketExpiredException with the specified previous occurred Exception.
     */
    public function __construct(string $message = "Ticket expired!", int $code = 1001, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}