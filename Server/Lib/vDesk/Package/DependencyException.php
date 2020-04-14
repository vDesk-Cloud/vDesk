<?php
declare(strict_types=1);

namespace vDesk\Package;

/**
 * Exception that is thrown when a Package dependency can't be resolved or satisfied in a required version.
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class DependencyException extends \Exception {
    
    /**
     * Initializes a new instance of the IOException class.
     *
     * @param string          $message  Initializes the IOException with the specified message.
     * @param int             $code     Initializes the IOException with the specified code.
     * @param \Throwable|null $previous Initializes the IOException with the specified previous occurred Exception.
     */
    public function __construct(string $message = "", int $code = 99, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
}