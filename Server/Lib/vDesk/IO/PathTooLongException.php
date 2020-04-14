<?php
declare(strict_types=1);

namespace vDesk\IO;

/**
 * Is thrown when a specified path exceeds the maximum supported length of paths.
 *
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 * @package vDesk\IO
 */
class PathTooLongException extends IOException {

    /**
     * Initializes a new instance of the PathTooLongException class.
     *
     * @param string          $message  Initializes the PathTooLongException with the specified message.
     * @param int             $code     Initializes the PathTooLongException with the specified code.
     * @param \Throwable|null $previous Initializes the PathTooLongException with the specified previous occurred Exception.
     */
    public function __construct(string $message = "", int $code = 15, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}