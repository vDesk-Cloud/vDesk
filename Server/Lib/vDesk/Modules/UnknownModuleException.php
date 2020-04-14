<?php
declare(strict_types=1);

namespace vDesk\Modules;

/**
 * Exception that is thrown when calling a Module that doesn't exist.
 *
 * @package vDesk\Modules
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class UnknownModuleException extends \Exception {
    /**
     * Initializes a new instance of the UnknownModuleException class.
     *
     * @param string          $message  Initializes the UnknownModuleException with the specified message.
     * @param int             $code     Initializes the UnknownModuleException with the specified code.
     * @param \Throwable|null $previous Initializes the UnknownModuleException with the specified previous occurred Exception.
     */
    public function __construct(string $message = "Module doesn't exist!", int $code = 1000, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
