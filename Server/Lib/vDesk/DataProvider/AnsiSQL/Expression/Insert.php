<?php
declare(strict_types=1);

namespace vDesk\DataProvider\AnsiSQL\Expression;

use vDesk\DataProvider\IResult;
use vDesk\DataProvider;
use vDesk\DataProvider\Expression\IInsert;

/**
 * Abstract base class for AnsiSQL compatible "INSERT" Expressions.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Insert implements IInsert {

    /**
     * The SQL-statement of the Insert\MariaDB.
     *
     * @var string
     */
    protected string $Statement = "";

    /**
     * The fields of the Insert\MariaDB.
     *
     * @var string[]
     */
    protected ?array $Fields = [];

    /**
     * @inheritDoc
     */
    public function Into(string $Table, array $Fields = null): static {
        $this->Statement .= "INSERT INTO " . DataProvider::SanitizeField($Table) . " ";
        $this->Fields    = $Fields;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Values(array $Values, array ...$Multiple): static {
        $this->Statement .= "("
                            . \implode(
                                ", ",
                                \array_map(static fn($Field): string => DataProvider::EscapeField($Field), $this->Fields ?? \array_keys($Values))
                            )
                            . ") VALUES ("
                            . \implode(
                                ", ",
                                \array_map(static fn($Value) => DataProvider::Sanitize($Value), \array_values($Values))
                            )
                            . ")";
        foreach($Multiple as $AdditionalValues) {
            $this->Statement .= ", ("
                                . \implode(
                                    ", ",
                                    \array_map(
                                        static fn($Value) => DataProvider::Sanitize($Value),
                                        \array_values($AdditionalValues)
                                    )
                                )
                                . ")";
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function ID(): int {
        $this->Execute();
        return DataProvider::LastInsertID();
    }

    /**
     * @inheritDoc
     */
    public function __invoke(): IResult|string|null {
        return $this->Execute()->ToValue();
    }

    /**
     * @inheritDoc
     */
    public function Execute(bool $Buffered = true): IResult {
        return DataProvider::Execute($this->Statement, $Buffered);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string {
        return $this->Statement;
    }

}