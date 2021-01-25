<?php
declare(strict_types=1);

namespace vDesk\DataProvider;

use vDesk\Struct\Collections\IEnumerable;

/**
 * Interface IResult
 * Represents a result-set retrieved by a SQL-data-provider.
 *
 * @property-read int Count Gets the amount of rows of the result-set.
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
interface IResult extends IEnumerable {

    /**
     * Retrieves a row of the IResult as an numeric array.
     *
     * @return string[]|null The row at the current position within the IResult; otherwise, null.
     */
    public function ToArray(): ?array;

    /**
     * Retrieves a row of the IResult as an associative array.
     *
     * @return string[]|null The row at the current position within the IResult; otherwise, null.
     */
    public function ToMap(): ?array;

    /**
     * Retrieves a row of the IResult as a single value.
     *
     * @return string|null The value of the row at the current position within the IResult; otherwise, null.
     */
    public function ToValue(): ?string;

    /**
     * Frees all resources allocated by the IResult.
     */
    public function Free(): void;

    /**
     * Gets a value indicating whether the previous executed SQL-statement was successful.
     *
     * @return bool True if the previous SQL-statement has been executed successfully; otherwise, false.
     */
    public function Successful(): bool;

    /**
     * Retrieves a row of the IResult as a single value.
     *
     * @return string|null The value of the row at the current position within the IResult; otherwise, null.
     */
    public function __invoke(): ?string;

    /**
     * Creates a set of IModels filled with the values of the rows of the IResult.
     *
     * @param string $Class The class to fill.
     *
     * @return iterable An iterable set of filled IModels.
     */
    public function Fill(string $Class): iterable;
}
