<?php
declare(strict_types=1);

namespace vDesk\DataProvider;

/**
 * Exception that is thrown when an error on a SQL-server has occurred.
 *
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @package vDesk\DataProvider
 */
class SQLException extends \Exception {
    
    /**
     * Initializes a new instance of the SQLException class.
     *
     * @param string          $message  Initializes the SQLException with the specified message.
     * @param int             $code     Initializes the SQLException with the specified code.
     * @param \Throwable|null $previous Initializes the SQLException with the specified previous occurred Exception.
     */
    public function __construct(string $message = "", int $code = 22, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
}