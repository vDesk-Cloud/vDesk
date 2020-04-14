<?php
declare(strict_types=1);

namespace vDesk\IO;

/**
 * Exception that is thrown when attempting to access a non existing file.
 *
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 * @package vDesk\IO
 */
class FileNotFoundException extends IOException {

    /**
     * Initializes a new instance of the FileNotFoundException class.
     *
     * @param string          $message  Initializes the FileNotFoundException with the specified message.
     * @param int             $code     Initializes the FileNotFoundException with the specified code.
     * @param \Throwable|null $previous Initializes the FileNotFoundException with the specified previous occurred Exception.
     */
    public function __construct(string $message = "", int $code = 13, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}