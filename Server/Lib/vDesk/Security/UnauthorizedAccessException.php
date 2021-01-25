<?php
declare(strict_types=1);

namespace vDesk\Security;

/**
 * Exception that is thrown when an operation is executed without having proper permissions.
 *
 * @package vDesk\Security
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class UnauthorizedAccessException extends \Exception {
    
    /**
     * Initializes a new instance of the UnauthorizedAccessException class.
     *
     * @param string          $message  Initializes the UnauthorizedAccessException with the specified message.
     * @param int             $code     Initializes the UnauthorizedAccessException with the specified code.
     * @param \Throwable|null $previous Initializes the UnauthorizedAccessException with the specified previous occurred Exception.
     */
    public function __construct(string $message = "Permission denied!", int $code = 1000, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
}
