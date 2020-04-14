<?php
declare(strict_types=1);

namespace vDesk\DataProvider;

/**
 * Represents an empty result set of a formerly executed query or procedure on a SQL database.
 * The empty result set represents rather the status of an INSERT, UPDATE or DELETE statement or stored-procedures without any return
 * values.
 *
 * @property-read int  Count  Gets the amount of rows of the result set.
 * @property-read bool Status Gets a value indicating whether the previous query or stored-procedure has been successfully executed.
 * @package vDesk\Connection\Database\Result
 * @author  Kerry Holz<DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
final class Result implements \Iterator, IResult {

    /**
     * Flag indicating whether the result set represents the result of a successfully executed statement.
     *
     * @var bool
     */
    public bool $Status = false;

    /**
     * Retrieves a row of the result set as an numeric array.
     * Note this method will always return an empty array.
     *
     * @return array An empty array.
     */
    public function ToArray(): ?array {
        return [];
    }

    /**
     *
     */
    public function Free(): void {
    }

    /**
     * Retrieves a row of the result set as an associative array.
     * Note this method will always return an empty array.
     *
     * @return array An empty array.
     */
    public function ToMap(): ?array {
        return [];
    }

    /**
     * Gets a value indicating whether the previous executed SQL-statement was successful.
     *
     * @return bool True if the previous SQL-statement has been executed successfully; otherwise, false.
     */
    public function Successful(): bool {
        return $this->Status;
    }

    /**
     * Retrieves a row of the IResult as a single value.
     *
     * @return string|null The value of the row at the current position within the IResult; otherwise, null.
     */
    public function ToValue(): ?string {
        return null;
    }

    /**
     * Creates a set of IModels filled with the values of the rows of the IResult.
     *
     * @param string $Class The class to fill.
     *
     * @return iterable An iterable set of filled IModels.
     */
    public function Fill(string $Class): iterable {
        return [];
    }

    /**
     * @ignore
     * Moves the internal result set-pointer forward.
     */
    public function next(): void {
    }

    /**
     * Retrieves the current row of the result set as an associative array.
     *
     * @return array|null The status of the previous executed statement or procedure.
     */
    public function current(): ?array {
        return null;
    }

    /**
     * @return int The current position of the internal result set-pointer.
     * @ignore
     * Gets the current position of the internal result set-pointer.
     * Note this method will alwys return 0, because an empty result set won't have any rows.
     */
    public function key(): int {
        return 0;
    }

    /**
     * @ignore
     * Resets the internal result set-pointer to the start.
     */
    public function rewind(): void {
    }

    /**
     * @return bool False, because an empty result set won't have any rows.
     * @ignore
     * Determines whether any left rows are available.
     * Note this method will always return false.
     */
    public function valid(): bool {
        return false;
    }
    
    /**
     * @inheritDoc
     */
    public function __invoke() {
        // TODO: Implement __invoke() method.
    }
}