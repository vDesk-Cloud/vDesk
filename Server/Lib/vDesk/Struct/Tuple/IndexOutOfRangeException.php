<?php
declare(strict_types=1);

namespace vDesk\Struct\Tuple;

/**
 * Exception that is thrown when accessing an index of a Tuple that doesn't exist.
 *
 * @package vDesk\Struct\Tuple
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class IndexOutOfRangeException extends \Exception {

    /**
     * Initializes a new instance of the IndexOutOfRangeException class.
     *
     * @param string          $message  Initializes the IndexOutOfRangeException with the specified message.
     * @param int             $code     Initializes the IndexOutOfRangeException with the specified code.
     * @param \Throwable|null $previous Initializes the IndexOutOfRangeException with the specified previous occurred Exception.
     */
    public function __construct(string $message = "", int $code = 3, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}