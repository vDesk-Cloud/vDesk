<?php
declare(strict_types=1);

namespace vDesk\Struct;

/**
 * Exception that is thrown when a private Property is accessed.
 *
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class AccessViolationException extends \Exception {
    
    /**
     * Initializes a new instance of the AccessViolationException class.
     *
     * @param string          $message  Initializes the AccessViolationException with the specified message.
     * @param int             $code     Initializes the AccessViolationException with the specified code.
     * @param \Throwable|null $previous Initializes the AccessViolationException with the specified previous occurred Exception.
     */
    public function __construct(string $message = "", int $code = 1, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
