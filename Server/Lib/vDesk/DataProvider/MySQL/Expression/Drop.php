<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MySQL\Expression;

use vDesk\DataProvider\IResult;
use vDesk\DataProvider;

/**
 * Represents a MySQL compatible "DROP" Expression.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Drop extends DataProvider\AnsiSQL\Expression\Drop {

    /**
     * Flag indicating whether the Database method has been called.
     *
     * @var bool
     */
    private bool $Database = false;

    /**
     * @inheritDoc
     */
    public function Execute(bool $Buffered = true): IResult {
        if($this->Database){
            return new DataProvider\Result(true);
        }
        return DataProvider::Execute($this->Statement, $Buffered);
    }

    /**
     * @inheritDoc
     */
    public function Database(string $Name): static {
        $this->Database = true;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Schema(string $Name): static {
        return parent::Database($Name);
    }

}