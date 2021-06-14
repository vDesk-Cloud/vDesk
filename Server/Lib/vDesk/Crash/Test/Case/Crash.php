<?php
declare(strict_types=1);

namespace vDesk\Crash\Test\Case;

use vDesk\Crash\Attribute;
use vDesk\Crash\Random;

/**
 * Attribute that represents a Test case crasher.
 *
 * @package vDesk\Crash
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
class Crash extends Attribute implements \IteratorAggregate {

    /**
     * Initializes a new instance of the Crash Attribute class.
     *
     * @param array    $Arguments Initializes the Crash Attribute with the specified set of arguments.
     * @param null|int $Amount    Initializes the Crash Attribute with the specified repetition amount.
     */
    public function __construct(public array $Arguments = [], public ?int $Amount = null) {
        parent::__construct($Arguments);
        $this->Amount ??= \random_int(1, 100);
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): \Generator {
        for($Index = 0; $Index < $this->Amount; $Index++) {
            yield $Index => Random::Value();
        }
    }

}