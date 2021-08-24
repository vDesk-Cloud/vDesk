<?php
declare(strict_types=1);

namespace vDesk\DataProvider\PgSQL\Expression;

/**
 * Represents a PgSQL compatible INSERT SQL expression.
 *
 * @package vDesk\DataProvider\PgSQL
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Insert extends \vDesk\DataProvider\AnsiSQL\Expression\Insert {

    /**
     * @inheritDoc
     */
    public function Values(array $Values, array ...$Multiple): static {
        foreach($this->Fields ?? \array_keys($Values) as $Index => $Field) {

            if($Field === "ID") {
                if(\array_key_exists("ID", $Values) && $Values["ID"] === null) {
                    $Values["ID"] = "DEFAULT";
                } else if(\array_key_exists($Index, $Values) && $Values[$Index] === null) {
                    $Values[$Index] = "DEFAULT";
                }

                foreach($Multiple as $MultipleValues) {
                    if($MultipleValues[$Index] === null) {
                        $MultipleValues[$Index] = "DEFAULT";
                    }
                }
                break;
            }
        }
        return parent::Values($Values, ...$Multiple);
    }
}