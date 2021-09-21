<?php
declare(strict_types=1);

namespace vDesk\DataProvider\MsSQL\Expression;

/**
 * Represents a MsSQL compatible "INSERT" Expression.
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
            //Omit null values.
            if($this->Fields[0] === $Identity) {
                unset($this->Fields[0], $Values[0]);
            } else {
                unset($Values[$Identity]);
            }
            foreach($Multiple as $Index => $MultipleValues) {
                if($MultipleValues[0] === null) {
                    unset($Multiple[$Index][0]);
                }
            }
        }
        return parent::Values($Values, ...$Multiple);
    }

}