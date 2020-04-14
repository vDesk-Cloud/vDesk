<?php
declare(strict_types=1);

namespace vDesk\Struct\Collections;

/**
 * Is Thrown when a non existent key of a Dictionary is being accessed.
 *
 * @package vDesk\Struct
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class KeyNotFoundException extends \Exception {

    /**
     * Initializes a new instance of the KeyNotFoundException class.
     *
     * @param string          $message  Initializes the KeyNotFoundException with the specified message.
     * @param int             $code     Initializes the KeyNotFoundException with the specified code.
     * @param \Throwable|null $previous Initializes the KeyNotFoundException with the specified previous occurred Exception.
     */
    public function __construct(string $message = "",int  $code = 5, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}