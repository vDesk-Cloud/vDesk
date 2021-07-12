<?php
declare(strict_types=1);

namespace vDesk\DataProvider\AnsiSQL\Expression;

use vDesk\DataProvider\Expression\ICreate;
use vDesk\DataProvider\IResult;
use vDesk\DataProvider;

/**
 * Represents a MySQL compatible CREATE SQL expression.
 *
 * @package vDesk\DataProvider\AnsiSQL
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Create implements ICreate {

    /**
     * The SQL-statement of the Create.
     *
     * @var string
     */
    protected string $Statement = "";

    /**
     * @inheritDoc
     */
    public function Database(string $Name): self {
        $this->Statement .= "CREATE DATABASE " . DataProvider::EscapeField($Name);
        return $this;
    }

    public function Index(string $Name, bool $Unique, array $Fields): self {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function On(string $Table): self {
        $this->Statement .= "ON " . DataProvider::SanitizeField($Table);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Execute(): IResult {
        return DataProvider::Execute($this->Statement);
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string {
        return $this->Statement;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(): IResult|string|null {
        return $this->Execute()->ToValue();
    }
}