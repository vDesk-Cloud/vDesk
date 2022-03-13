<?php
declare(strict_types=1);

namespace vDesk\DataProvider\PgSQL\Expression;

/**
 * Represents a PgSQL compatible "INSERT" Expression.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Insert extends \vDesk\DataProvider\AnsiSQL\Expression\Insert {

    /**
     * @inheritDoc
     */
    public function Values(array $Values, array ...$Multiple): static {
        //Assume the first field is an identity field.
        $Fields   = $this->Fields ?? \array_keys($Values);
        $Identity = \current($Fields);
        if(\current($Values) === null && \str_ends_with($Identity, "ID")) {
            if(($this->Fields[0] ?? null) === $Identity) {
                $Values[0] = "DEFAULT";
            } else {
                $Values[$Identity] = "DEFAULT";
            }
            foreach($Multiple as $Index => $MultipleValues) {
                if($MultipleValues[0] === null) {
                    $Multiple[$Index][0] = "DEFAULT";
                }
            }
        }
        return parent::Values($Values, ...$Multiple);
    }
}