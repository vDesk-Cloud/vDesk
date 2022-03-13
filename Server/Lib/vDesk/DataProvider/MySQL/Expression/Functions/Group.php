<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression\Functions;

use vDesk\DataProvider;
use vDesk\DataProvider\Expression\IAggregateFunction;

/**
 * Represents a MySQL compatible GROUP_CONCAT function.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Group implements IAggregateFunction {

    /**
     * The name of the function.
     */
    protected const Name = "GROUP_CONCAT";

    /**
     * The fields of the Group function.
     *
     * @var string[]
     */
    protected array $Fields;

    /**
     * Initializes a new instance of the Group class.
     *
     * @param string ...$Fields Initializes the Group with the specified fields.
     */
    public function __construct(string ...$Fields) {
        $this->Fields = $Fields;
    }

    /**
     * Returns the string representation of the IAggregateFunction.
     *
     * @return string The string representation of the IAggregateFunction.
     */
    public function __toString(): string {
        return self::Name . "(" . \implode(" ,", \array_map(static fn(string $Field): string => DataProvider::SanitizeField($Field), $this->Fields)) . ")";
    }
}