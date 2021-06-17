<?php
declare(strict_types=1);

namespace vDesk\Crash\Test;

use vDesk\Crash\Attribute;

/**
 * Attribute that represents a repeatable Test or case.
 *
 * @package vDesk\Crash
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Repeat extends Attribute implements \IteratorAggregate {

    /**
     * Initializes a new instance of the Repeat Attribute class.
     *
     * @param int      $Amount   Initializes the Repeat Attribute with the specified repetition amount.
     * @param null|int $Interval Initializes the Repeat Attribute with the specified interval in microseconds.
     */
    public function __construct(public int $Amount = 10, public ?int $Interval = null) {}

    /**
     * @inheritDoc
     */
    public function getIterator(): \Generator {
        if($this->Interval !== null) {
            for($Iteration = 0; $Iteration < $this->Amount; $Iteration++) {
                yield $Iteration;
                \usleep($this->Interval);
            }
        } else {
            for($Iteration = 0; $Iteration < $this->Amount; $Iteration++) {
                yield $Iteration;
            }
        }
    }

}