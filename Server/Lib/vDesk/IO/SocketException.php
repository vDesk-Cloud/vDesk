<?php
declare(strict_types=1);

namespace vDesk\IO;

/**
 * Exception that is thrown when a connection error occurred.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class SocketException extends IOException {

    /**
     * Initializes a new instance of the SocketException class.
     *
     * @param string          $message  Initializes the SocketException with the specified message.
     * @param int             $code     Initializes the SocketException with the specified code.
     * @param \Throwable|null $previous Initializes the SocketException with the specified previous occurred Exception.
     */
    public function __construct(string $message = "", int $code = 16, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}