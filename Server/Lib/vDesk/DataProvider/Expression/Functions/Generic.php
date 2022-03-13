<?php
declare(strict_types=1);

namespace vDesk\DataProvider\Expression\Functions;

use vDesk\DataProvider\Expression\IAggregateFunction;
use vDesk\DataProvider;

/**
 * Represents a generic SQL aggregate function.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Generic implements IAggregateFunction {

    /**
     * The values of the AggregateFunction\MariaDB.
     *
     * @var IAggregateFunction[]|array
     */
    protected array $Values;

    /**
     * Flag indicating whether the values of the
     *
     * @var bool
     */
    protected bool $EscapeFields;

    /**
     * The values of the AggregateFunction\MariaDB.
     *
     * @var bool
     */
    protected bool $EscapeValues;

    /**
     * Initializes a new instance of the AggregateFunction\MariaDB class.
     *
     * @param string $Name      Initializes the Generic with the specified name.
     * @param mixed  ...$Values Initializes the Generic with the specified set of values.
     */
    public function __construct(protected string $Name, ...$Values) {
        $this->Values = $Values;
    }

    /**
     * Returns the string representation of the AggregateFunction\MariaDB.
     *
     * @return string The string representation of the AggregateFunction\MariaDB.
     */
    public function __toString(): string {

        $Values  = [];
        $Matches = [];

        foreach($this->Values as $Value) {

            if($Value instanceof IAggregateFunction) {
                $Values[] = (string)$Value;
                continue;
            }

            if(\is_string($Value) && (int)\preg_match(DataProvider\AnsiSQL\Provider::SeparatorExpression, $Value, $Matches) > 0) {
                $Values[] = DataProvider::EscapeField($Matches[1]) . DataProvider::$Separator . DataProvider::EscapeField($Matches[2]);
                continue;
            }

            $Values[] = DataProvider::Sanitize($Value);

        }

        return \strtoupper($this->Name) . "(" . \implode(", ", $Values) . ")";

    }
}