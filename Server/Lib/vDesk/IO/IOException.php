<?php
declare(strict_types = 1);

namespace vDesk\IO;

/**
 * Exception that is thrown when I/O-related errors occurred.
 * @author Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class IOException extends \Exception {
    /**
     * Initializes a new instance of the IOException class.
     *
     * @param string          $message  Initializes the IOException with the specified message.
     * @param int             $code     Initializes the IOException with the specified code.
     * @param \Throwable|null $previous Initializes the IOException with the specified previous occurred Exception.
     */
    public function __construct(string $message = "", int $code = 10, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}