<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression\Functions;

use vDesk\DataProvider\Expression\IAggregateFunction;
use vDesk\DataProvider;

/**
 * Represents a distinct SQL function.
 *
 * @package vDesk\DataProvider\Expression\Functions\MariaDB
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
abstract class Distinct implements IAggregateFunction {
    
    /**
     * The name of the Distinct.
     */
    protected const Name = "";
    
    /**
     * The field of the Distinct.
     *
     * @var string
     */
    protected string $Field;
    
    /**
     * Flag indicating whether to find the minimum of the distinct values of of the field of the Distinct.
     *
     * @var bool
     */
    protected bool $Distinct;
    
    /**
     * Initializes a new instance of the Distinct class.
     *
     * @param mixed $Field The field
     * @param bool  $Distinct
     */
    public function __construct(string $Field, bool $Distinct = false) {
        $this->Field    = $Field;
        $this->Distinct = $Distinct;
    }
    
    /**
     * Returns the string representation of the Distinct.
     *
     * @return string The string representation of the Distinct.
     */
    public function __toString(): string {
        return static::Name . "(" . ($this->Distinct ? "DISTINCT " : "") . DataProvider::EscapeField($this->Field) . ")";
    }
    
}