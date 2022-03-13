<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL;

use vDesk\DataProvider\IResult;
use vDesk\IO\IOException;
use vDesk\Struct\Collections\Collection;
use vDesk\Struct\Collections\IEnumerable;
use vDesk\Struct\Properties;

/**
 * Represents a result set of an executed query or procedure on a MySQL or MariaDB database.
 *
 * @property-read int  Count    Gets the amount of rows of the result set.
 * @property-read bool Status   Gets a value indicating whether the previous query or stored-procedure has been successfully executed.
 * @property-read bool Buffered Gets a value indicating whether the result set is buffered.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Result implements \Iterator, IResult {

    use Properties;

    /**
     * The current position of the internal result set pointer.
     *
     * @var int
     */
    private int $Position = 0;

    /**
     * Flag indicating whether all associated resources of the result has been disposed.
     *
     * @var bool
     */
    private bool $Disposed = false;

    /**
     * Flag indicating whether the result set represents the result of a successfully executed statement.
     *
     * @var bool
     */
    public bool $Status = true;

    /**
     * Initializes a new instance of the Result class.
     *
     * @param \mysqli_result $ResultSet The Initializes the Result with the specified result set.
     * @param bool           $Buffered  Flag indicating whether the result set will be buffered.
     */
    public function __construct(protected \mysqli_result $ResultSet, public bool $Buffered = true) {
        $this->AddProperty("Count", [\Get => fn(): int => $this->ResultSet->num_rows ?? 0]);
    }

    /**
     * Retrieves a row of the result set as an numeric array.
     *
     * @return string[]|null The row at the current position within the result set; otherwise, null.
     */
    public function ToArray(): ?array {
        return $this->ResultSet->fetch_row();
    }

    /**
     * Frees all resources allocated by this result.
     */
    public function Free(): void {
        if(!$this->Disposed) {
            $this->ResultSet->free_result();
            $this->Disposed = true;
        }
    }

    /**
     * Retrieves a row of the result set as an associative array.
     *
     * @return string[]|null The row at the current position within the result set; otherwise, null.
     */
    public function ToMap(): ?array {
        return $this->ResultSet->fetch_assoc();
    }

    /**
     * Gets a value indicating whether the previous executed SQL-statement was successful.
     *
     * @return bool True if the previous SQL-statement has been executed successfully; otherwise, false.
     */
    public function Successful(): bool {
        return true;
    }

    /**
     * Retrieves a row of the IResult as a single value.
     *
     * @return string|null The value of the row at the current position within the IResult; otherwise, null.
     */
    public function ToValue(): ?string {
        return $this->ToArray()[0] ?? null;
    }

    /**
     * Retrieves a row of the IResult as a single value.
     *
     * @return string|null The value of the row at the current position within the IResult; otherwise, null.
     */
    public function __invoke(): ?string {
        return $this->ToValue();
    }

    /**
     * @ignore
     */
    public function __destruct() {
        $this->Free();
    }

    /**
     * @ignore
     * Moves the internal result set-pointer forward.
     */
    public function next(): void {
        $this->Position++;
    }

    /**
     * Retrieves the current row of the result set as an associative array.
     *
     * @return array|null The row at the current position within the result set; otherwise, null.
     */
    public function current(): ?array {
        return $this->ToMap();
    }

    /**
     * Gets the current position of the internal result set-pointer.
     * @ignore
     * @return int The current position of the internal result set-pointer.
     */
    public function key(): int {
        return $this->Position;
    }

    /**
     * Resets the internal result set-pointer to the start.
     * @ignore
     * @throws \vDesk\IO\IOException Thrown if the result set is being streamed.
     */
    public function rewind(): void {
        if(!$this->Buffered) {
            throw new IOException("Cannot rewind unbuffered result set!");
        }
        $this->ResultSet->data_seek(0);
        $this->Position = 0;
    }

    /**
     * Determines whether any left rows are available.
     * @ignore
     * @return bool True if any rows are left; otherwise, false.
     */
    public function valid(): bool {
        if($this->Buffered) {
            return $this->ResultSet->data_seek($this->Position);
        }
        return $this->Position < $this->ResultSet->num_rows;
    }

    /**
     * @inheritDoc
     */
    public function Count(): int {
        return $this->ResultSet->num_rows;
    }

    /**
     * @inheritDoc
     */
    public function Find(callable $Predicate): mixed {
        return (new Collection($this))->Find($Predicate);
    }

    /**
     * @inheritDoc
     */
    public function Sort(callable $Predicate): bool {
        return (new Collection($this))->Sort($Predicate);
    }

    /**
     * @inheritDoc
     */
    public function Filter(callable $Predicate): IEnumerable {
        return (new Collection($this))->Filter($Predicate);
    }

    /**
     * @inheritDoc
     */
    public function Map(callable $Predicate): IEnumerable {
        return (new Collection($this))->Map($Predicate);
    }

    /**
     * @inheritDoc
     */
    public function Reduce(callable $Predicate, $InitialValue = null): mixed {
        return (new Collection($this))->Reduce($Predicate, $InitialValue);
    }

    /**
     * @inheritDoc
     */
    public function Any(callable $Predicate): bool {
        return (new Collection($this))->Any($Predicate);
    }

    /**
     * @inheritDoc
     */
    public function Every(callable $Predicate): bool {
        return (new Collection($this))->Every($Predicate);
    }
}