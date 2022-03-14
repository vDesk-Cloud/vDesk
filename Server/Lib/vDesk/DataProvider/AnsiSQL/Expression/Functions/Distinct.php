<?php
declare(strict_types=1);

namespace vDesk\DataProvider\AnsiSQL\Expression\Functions;

use vDesk\DataProvider\Expression\IAggregateFunction;
use vDesk\DataProvider;

/**
 * Represents a distinct SQL aggregate function.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Distinct implements IAggregateFunction {

    /**
     * The name of the Distinct.
     */
    protected const Name = "";

    /**
     * Initializes a new instance of the Distinct class.
     *
     * @param mixed $Field    Initializes the Distinct with the specified field the function applies to.
     * @param bool  $Distinct Initializes the Distinct with the specified flag indicating whether to prepend a "DISTINCT" statement to the function.
     */
    public function __construct(protected string $Field, protected bool $Distinct = false) {
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