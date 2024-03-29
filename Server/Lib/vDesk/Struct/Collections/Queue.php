<?php
declare(strict_types=1);

namespace vDesk\Struct\Collections;

use vDesk\Struct\Properties;

/**
 * Represents a variable size first-in-first-out (FIFO) collection.
 *
 * @property-read int $Count Gets the amount of elements in the Queue.
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Queue implements \IteratorAggregate, IEnumerable {

    use Properties;

    /**
     * The elements of the Queue.
     *
     * @var array
     */
    protected array $Elements = [];

    /**
     * Initializes a new instance of the Queue class.
     *
     * @param iterable $Elements Initializes the Queue with the specified set of elements.
     */
    public function __construct(iterable $Elements = []) {
        $this->AddProperty("Count", [\Get => fn(): int => \count($this->Elements)]);
        foreach($Elements as $Value) {
            $this->Enqueue($Value);
        }
    }

    /**
     * Inserts an element at the end of the Queue.
     *
     * @param mixed $Element The element to add.
     */
    public function Enqueue(mixed $Element): void {
        $this->Elements[] = $Element;
    }

    /**
     * Removes and returns the element at the beginning of the Queue.
     *
     * @return mixed The element removed from the beginning of the Queue; otherwise if the Queue is emtpy, null.
     */
    public function Dequeue(): mixed {
        return \array_shift($this->Elements);
    }

    /**
     * Returns the element at the beginning of the Queue without removing it.
     *
     * @return mixed The element at the beginning of the Queue; otherwise if the Queue is emtpy, null.
     */
    public function Peek(): mixed {
        return empty($this->Elements) ? null : \reset($this->Elements);
    }

    /** @inheritDoc */
    public function Clear(): void {
        \array_splice($this->Elements, 0, \count($this->Elements));
    }

    /**
     * Returns a Generator which iterates over the Queue.
     *
     * @return \Generator A Generator which iterates over the Queue.
     * @ignore
     */
    public function getIterator(): \Generator {
        while(($Key = \key($this->Elements)) !== null) {
            yield $Key => $this->Dequeue();
        }
    }

    /** @inheritDoc */
    public function Count(): int {
        return \count($this->Elements);
    }

    /** @inheritDoc */
    public function Filter(callable $Predicate): static {
        $Queue = new static();
        foreach($this->Elements as $Index => $Value) {
            if($Predicate($Value, $Index, $this)) {
                $Queue->Enqueue($Value);
            }
        }
        return $Queue;
    }

    /** @inheritDoc */
    public function Find(callable $Predicate): mixed {
        foreach($this->Elements as $Index => $Value) {
            if($Predicate($Value, $Index, $this)) {
                return $Value;
            }
        }
        return null;
    }

    /** @inheritDoc */
    public function Every(callable $Predicate): bool {
        foreach($this->Elements as $Index => $Value) {
            if(!$Predicate($Value, $Index, $this)) {
                return false;
            }
        }
        return true;
    }

    /** @inheritDoc */
    public function Sort(callable $Predicate): static {
        $Sorted = $this->ToArray();
        \usort($Sorted, $Predicate);
        return new static($Sorted);
    }

    /** @inheritDoc */
    public function Map(callable $Predicate): static {
        $Queue = new static();
        foreach($this->Elements as $Index => $Value) {
            $Queue->Enqueue($Predicate($Value, $Index, $this));
        }
        return $Queue;
    }

    /** @inheritDoc */
    public function Reduce(callable $Predicate, $InitialValue = null): mixed {
        $Accumulator = $InitialValue ?? \reset($this->Elements);
        foreach($this->Elements as $Index => $Value) {
            $Accumulator = $Predicate($Accumulator, $Value, $Index, $this);
        }
        return $Accumulator;
    }

    /** @inheritDoc */
    public function Any(callable $Predicate): bool {
        foreach($this->Elements as $Index => $Value) {
            if($Predicate($Value, $Index, $this)) {
                return true;
            }
        }
        return false;
    }

}