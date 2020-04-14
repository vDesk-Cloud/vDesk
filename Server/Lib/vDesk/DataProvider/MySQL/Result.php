<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL;

use vDesk\DataProvider\IResult;
use vDesk\Struct\Properties;

/**
 * Represents a result set of an executed query or procedure on a MySQL or MariaDB database.
 *
 * @property-read int  Count    Gets the amount of rows of the result set.
 * @property-read bool Status   Gets a value indicating whether the previous query or stored-procedure has been successfully executed.
 * @property-read bool Buffered Gets a value indicating whether the result set is buffered.
 * @package vDesk\DataProvider\Result
 * @author  Kerry Holz<DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class Result implements \Iterator, IResult {

    use Properties;

    /**
     * The result set of previous executed query or procedure.
     *
     * @var \mysqli_result|null
     */
    private ?\mysqli_result $ResultSet;

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
     * Flag indicating whether the result set is buffered.
     *
     * @var bool
     */
    private bool $Buffered;

    /**
     * Flag indicating whether the result set represents the result of a successfully executed statement.
     *
     * @var bool
     */
    public bool $Status = true;

    /**
     * Initializes a new instance of the MariaDB class.
     *
     * @param \mysqli_result $ResultSet The Initializes the MariaDB with the specified result set.
     * @param bool           $Buffered  Flag indicating whether the result set will be buffered.
     */
    public function __construct(\mysqli_result $ResultSet = null, bool $Buffered = true) {
        $this->ResultSet = $ResultSet;
        $this->Buffered  = $Buffered;
        $this->AddProperties([
            "Count"    => [\Get => fn(): int => $this->ResultSet !== null ? $this->ResultSet->num_rows : 0],
            "Buffered" => [\Get => fn(): bool => $this->Buffered]
        ]);
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
    public function __invoke() {
        return $this->ToValue();
    }

    /**
     * Creates a set of IModels filled with the values of the rows of the IResult.
     *
     * @param string $Class The class to fill.
     *
     * @return iterable An iterable set of filled IModels.
     */
    public function Fill(string $Class): iterable {
        foreach($this as $Row) {
            yield $Class::FromResultRow($Row);
        }
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
        if($this->Buffered) {
            $this->ResultSet->data_seek($this->Position);
        }
    }

    /**
     * Retrieves the current row of the result set as an associative array.
     *
     * @return array|null The row at the current position within the result set; otherwise, null.
     */
    public function current(): ?array {
        if($this->Buffered) {
            $this->ResultSet->data_seek($this->Position);
        }
        return $this->ToMap();
    }

    /**
     * @return int The current position of the internal result set-pointer.
     * @ignore
     * Gets the current position of the internal result set-pointer.
     */
    public function key(): int {
        return $this->Position;
    }

    /**
     * @ignore
     * Resets the internal result set-pointer to the start.
     */
    public function rewind(): void {
        if($this->Buffered) {
            $this->ResultSet->data_seek(0);
        }
        $this->Position = 0;
    }

    /**
     * @return bool True if any rows are left; otherwise, false.
     * @ignore
     * Determines whether any left rows are available.
     */
    public function valid(): bool {
        if($this->Buffered) {
            return $this->ResultSet->data_seek($this->Position);
        }
        return $this->Position < $this->ResultSet->num_rows ?? false;
    }
}