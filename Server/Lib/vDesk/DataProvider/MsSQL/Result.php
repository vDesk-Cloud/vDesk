<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MsSQL;

use vDesk\DataProvider\IResult;
use vDesk\IO\IOException;
use vDesk\Struct\Properties;

/**
 * Represents a result set of an executed query or procedure on a PostgreSQL database.
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
     * The amount of rows of the Result.
     *
     * @var int
     */
    private int $Count;

    /**
     * Initializes a new instance of the Result class.
     *
     * @param null|resource $ResultSet Initializes the Result with the specified result set.
     */
    public function __construct(protected mixed $ResultSet, protected bool $Buffered = true) {
        $this->Count = \max((int)\sqlsrv_num_rows($this->ResultSet), 0);
        $this->AddProperty("Count", [\Get => fn(): int => $this->Count]);
    }

    /**
     * Retrieves a row of the result set as an numeric array.
     *
     * @return string[]|null The row at the current position within the result set; otherwise, null.
     */
    public function ToArray(): ?array {
        if($this->Count === 0){
            return null;
        }
        if(!$this->Buffered) {
            return \sqlsrv_fetch_array($this->ResultSet, \SQLSRV_FETCH_NUMERIC);
        }
        return \sqlsrv_fetch_array($this->ResultSet, \SQLSRV_FETCH_NUMERIC, \SQLSRV_SCROLL_ABSOLUTE, $this->Position);
    }

    /**
     * Retrieves a row of the result set as an associative array.
     *
     * @return string[]|null The row at the current position within the result set; otherwise, null.
     */
    public function ToMap(): ?array {
        if($this->Count === 0){
            return null;
        }
        if(!$this->Buffered) {
            return \sqlsrv_fetch_array($this->ResultSet, \SQLSRV_FETCH_ASSOC);
        }
        return \sqlsrv_fetch_array($this->ResultSet, \SQLSRV_FETCH_ASSOC, \SQLSRV_SCROLL_ABSOLUTE, $this->Position);
    }

    /**
     * Retrieves a row of the IResult as a single value.
     *
     * @return null|string|int|float The value of the row at the current position within the IResult; otherwise, null.
     */
    public function ToValue(): null|string|int|float {
        return $this->ToArray()[0] ?? null;
    }

    /**
     * Gets a value indicating whether the previous executed SQL-statement was successful.
     *
     * @return bool True if the previous SQL-statement has been executed successfully; otherwise, false.
     */
    public function Successful(): bool {
        return $this->ResultSet !== false;
    }

    /**
     * Frees all resources allocated by this result.
     */
    public function Free(): void {
        if(!$this->Disposed && \is_resource($this->ResultSet)) {
            \sqlsrv_free_stmt($this->ResultSet);
            $this->Disposed = true;
        }
    }

    /**
     * Retrieves a row of the IResult as a single value.
     *
     * @return null|string|int|float The value of the row at the current position within the IResult; otherwise, null.
     */
    public function __invoke(): null|string|int|float {
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
     *
     * @return int The current position of the internal result set-pointer.
     * @ignore
     */
    public function key(): int {
        return $this->Position;
    }

    /**
     * Resets the internal result set-pointer to the start.
     *
     * @throws \vDesk\IO\IOException Thrown if the result set is being streamed.
     * @ignore
     */
    public function rewind(): void {
        if(!$this->Buffered) {
            throw new IOException("Cannot rewind unbuffered result set!");
        }
        $this->Position = 0;
    }

    /**
     * Determines whether any left rows are available.
     *
     * @return bool True if any rows are left; otherwise, false.
     * @ignore
     */
    public function valid(): bool {
        return $this->Position < $this->Count;
    }

    /**
     * @inheritDoc
     */
    public function Count(): int {
        return \sqlsrv_num_rows($this->ResultSet);
    }

}