<?php
declare(strict_types=1);

namespace vDesk\Data;

/**
 * Exception that is thrown when attempting to fill a virtual {@link \vDesk\Data\IModel}.
 *
 * @package vDesk\Data
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class IDNullException extends \Exception {
    
    /**
     * Initializes a new instance of the IDNullException class.
     *
     * @param string          $message  Initializes the IDNullException with the specified message.
     * @param int             $code     Initializes the IDNullException with the specified code.
     * @param \Throwable|null $previous Initializes the IDNullException with the specified previous occurred Exception.
     */
    public function __construct(string $message = "Cannot Fill Model without ID", int $code = 51, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
}