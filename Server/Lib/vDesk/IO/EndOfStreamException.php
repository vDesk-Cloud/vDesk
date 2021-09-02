<?php
declare(strict_types=1);

namespace vDesk\IO;

/**
 * Exception that is thrown when attempting to read from a Stream which has reached its end.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class EndOfStreamException extends IOException {

    /**
     * Initializes a new instance of the EndOfStreamException class.
     *
     * @param string          $message  Initializes the EndOfStreamException with the specified message.
     * @param int             $code     Initializes the EndOfStreamException with the specified code.
     * @param \Throwable|null $previous Initializes the EndOfStreamException with the specified previous occurred Exception.
     */
    public function __construct(string $message = "", int $code = 14, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}