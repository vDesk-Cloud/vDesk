<?php
declare(strict_types=1);

namespace vDesk\Struct\Collections;

/**
 * Exception that is thrown when a value with a duplicate key is inserted in a Dictionary.
 *
 * @package vDesk\Struct\Collections\Typed
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class DuplicateKeyException extends \Exception {

    /**
     * Initializes a new instance of the DuplicateKeyException class.
     *
     * @param string          $message  Initializes the DuplicateKeyException with the specified message.
     * @param int             $code     Initializes the DuplicateKeyException with the specified code.
     * @param \Throwable|null $previous Initializes the DuplicateKeyException with the specified previous occurred Exception.
     */
    public function __construct(string $message = "", int $code = 6, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}