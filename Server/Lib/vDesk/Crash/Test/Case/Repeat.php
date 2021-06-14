<?php
declare(strict_types=1);

namespace vDesk\Crash\Test\Case;

use vDesk\Crash\Attribute;

/**
 * Attribute that represents a repeatable Test or case.
 *
 * @package vDesk\Crash
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Repeat extends Attribute implements \IteratorAggregate {

    /**
     * Initializes a new instance of the Repeat Attribute class.
     *
     * @param array $Arguments Initializes the Repeat Attribute with the specified set of arguments.
     * @param int   $Amount    Initializes the Repeat Attribute with the specified repetition amount.
     */
    public function __construct(public array $Arguments = [], public int $Amount = 10) {
        parent::__construct($Arguments);
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): \Generator {
        for($Iteration = 0; $Iteration < $this->Amount; $Iteration++) {
            yield $Iteration;
        }
    }

}