<?php
declare(strict_types=1);

namespace vDesk\DataProvider\PgSQL\Expression;

use vDesk\DataProvider;

/**
 * Represents a PgSQL compatible "DROP" Expression.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Drop extends \vDesk\DataProvider\AnsiSQL\Expression\Drop {

    /**
     * Flag indicating whether a primary index should be dropped.
     *
     * @var bool
     */
    protected bool $Primary = false;

    /** @inheritDoc */
    public function Index(string $Name): static {
        if($Name === "Primary"){
            $this->Primary = true;
            return $this;
        }
        return parent::Index($Name);
    }

    /** @inheritDoc */
    public function On(string $Table): static {
        if($this->Primary){

            foreach(\explode(DataProvider\PgSQL\Provider::Separator, $Table) as $Part){
                $Name = $Part;
            }

            $this->Statement = "ALTER TABLE " .  DataProvider::SanitizeField($Table) . " DROP CONSTRAINT " . DataProvider::EscapeField($Name . "_pkey");
        }
        return $this;
    }
}