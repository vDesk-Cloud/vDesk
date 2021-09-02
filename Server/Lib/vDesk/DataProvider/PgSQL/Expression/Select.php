<?php
declare(strict_types=1);

namespace vDesk\DataProvider\PgSQL\Expression;

use vDesk\DataProvider\PgSQL\Expression;

/**
 * Represents a PgSQL compatible SELECT SQL expression.
 *
 * @package vDesk\DataProvider\PgSQL
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Select extends \vDesk\DataProvider\AnsiSQL\Expression\Select {

    //Postgres' SELECT is (mostly) ANSI conform.

    /**
     * @inheritDoc
     */
    public function Where(array ...$Conditions): static {
        $this->Statement .= "WHERE " . Expression::TransformConditions($this->Aliases, ...$Conditions) . " ";
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function On(array ...$Fields): static {
        $this->Statement .= "ON " . Expression::TransformConditions($this->Aliases, ...$Fields) . " ";
        return $this;
    }

}