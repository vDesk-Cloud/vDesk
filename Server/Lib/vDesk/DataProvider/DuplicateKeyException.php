<?php
declare(strict_types=1);

namespace vDesk\DataProvider;

/**
 * Exception that is thrown when a SQL-statement tries to insert a duplicate key on an unique column.
 *
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 * @package vDesk\Connection
 */
class DuplicateKeyException extends SQLException {

    /**
     * Initializes a new instance of the DuplicateKeyException class.
     *
     * @param string          $message  Initializes the DuplicateKeyException with the specified message.
     * @param int             $code     Initializes the DuplicateKeyException with the specified code.
     * @param \Throwable|null $previous Initializes the DuplicateKeyException with the specified previous occurred Exception.
     */
    public function __construct(string $message = "", int $code = 23, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
