<?php
declare(strict_types=1);

namespace vDesk\IO;

/**
 * Exception that is thrown when a corrupted file is read.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class CorruptFileException extends IOException {

    /**
     * Initializes a new instance of the CorruptFileException class.
     *
     * @param string          $message  Initializes the CorruptFileException with the specified message.
     * @param int             $code     Initializes the CorruptFileException with the specified code.
     * @param \Throwable|null $previous Initializes the CorruptFileException with the specified previous occurred Exception.
     */
    public function __construct(string $message = "", int $code = 11, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}