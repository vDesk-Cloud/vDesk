<?php
declare(strict_types=1);

namespace vDesk\IO;

/**
 * Exception that is thrown when attempting to access a non existing directory.
 *
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 * @package vDesk\IO
 */
class DirectoryNotFoundException extends IOException {

    /**
     * Initializes a new instance of the DirectoryNotFoundException class.
     *
     * @param string          $message  Initializes the DirectoryNotFoundException with the specified message.
     * @param int             $code     Initializes the DirectoryNotFoundException with the specified code.
     * @param \Throwable|null $previous Initializes the DirectoryNotFoundException with the specified previous occurred Exception.
     */
    public function __construct(string $message = "", int $code = 12, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}