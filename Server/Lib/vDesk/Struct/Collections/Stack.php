<?php
declare(strict_types=1);

namespace vDesk\Struct\Collections;

use vDesk\Struct\Collections\IEnumerable;
use vDesk\Struct\Properties;

/**
 * Represents a variable size last-in-first-out (LIFO) collection.
 *
 * @property-read int $Count Gets the amount of elements in the Stack.
 * @package vDesk\Struct\Collections
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Stack implements \IteratorAggregate, IEnumerable {
    
    use Properties;
    
    /**
     * The elements of the Stack.
     *
     * @var mixed[]
     */
    protected array $Elements = [];
    
    /**
     * Initializes a new instance of the Stack class.
     *
     * @param iterable $Elements Initializes the Stack with the specified set of elements.
     */
    public function __construct(iterable $Elements = []) {
        $this->AddProperty("Count",
            [
                \Get => fn(): int => \count($this->Elements)
            ]);
        foreach($Elements as $Value) {
            $this->Push($Value);
        }
    }
    
    /**
     * Inserts an element at the top of the Stack.
     *
     * @param mixed $Element The element to add.
     */
    public function Push($Element): void {
        $this->Elements[] = $Element;
        \end($this->Elements);
    }
    
    /**
     * Removes and returns the element at the top of the Stack.
     *
     * @return null|mixed The element removed from the top of the Stack; otherwise if the Stack is emtpy, null.
     */
    public function Pop() {
        $Value = array_pop($this->Elements);
        \end($this->Elements);
        return $Value;
    }
    
    /**
     * Returns the element at the top of the Stack without removing it.
     *
     * @return null|mixed The element at the top of the Stack; otherwise if the Stack is emtpy, null.
     */
    public function Peek() {
        return empty($this->Elements) ? null : \end($this->Elements);
    }
    
    /**
     * Removes all elements from the Stack.
     */
    public function Clear(): void {
        \array_splice($this->Elements, 0, \count($this->Elements));
    }
    
    /**
     * Determines whether an element is in the Stack.
     *
     * @param mixed $Element The element to check.
     *
     * @return bool True if the element is in the Stack; otherwise, false.
     */
    public function Contains($Element): bool {
        return \in_array($Element, $this->Elements);
    }
    
    /**
     * Returns a \Generator which iterates over the Stack.
     *
     * @return \Generator
     * @ignore
     */
    public function getIterator(): \Generator {
        while(($mKey = \key($this->Elements)) !== null) {
            yield $mKey => $this->Pop();
        }
    }
    
    /**
     * @inheritDoc
     * @see \vDesk\Struct\Collections\IEnumerable::Count()
     */
    public function Count(): int {
        return \count($this->Elements);
    }
    
    /**
     * @inheritDoc
     * @see \vDesk\Struct\Collections\IEnumerable::Filter()
     */
    public function Filter(callable $Predicate): IEnumerable {
        $Stack = new static();
        foreach($this->Elements as $Index => $Value) {
            if($Predicate($Value, $Index, $this)) {
                $Stack->Push($Value);
            }
        }
        return $Stack;
    }
    
    /**
     * @inheritDoc
     * @see \vDesk\Struct\Collections\IEnumerable::Find()
     */
    public function Find(callable $Predicate): mixed {
        foreach($this->Elements as $Index => $Value) {
            if($Predicate($Value, $Index, $this)) {
                return $Value;
            }
        }
        return null;
    }
    
    /**
     * @inheritDoc
     * @see \vDesk\Struct\Collections\IEnumerable::Every()
     */
    public function Every(callable $Predicate): bool {
        foreach($this->Elements as $Index => $Value) {
            if(!$Predicate($Value, $Index, $this)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * @inheritDoc
     * @see \vDesk\Struct\Collections\IEnumerable::Sort()
     */
    public function Sort(callable $Predicate): bool {
        return \usort($this->Elements, $Predicate);
    }
    
    /**
     * @inheritDoc
     * @see \vDesk\Struct\Collections\IEnumerable::Map()
     */
    public function Map(callable $Predicate): IEnumerable {
        $Stack = new static();
        foreach($this->Elements as $Index => $Value) {
            $Stack->Push($Predicate($Value, $Index, $this));
        }
        return $Stack;
    }
    
    /**
     * @inheritDoc
     * @see \vDesk\Struct\Collections\IEnumerable::Reduce()
     */
    public function Reduce(callable $Predicate, $InitialValue = null): mixed {
        $Accumulator = $InitialValue ?? \reset($this->Elements);
        foreach($this->Elements as $Index => $Value) {
            $Accumulator = $Predicate($Accumulator, $Value, $Index, $this);
        }
        return $Accumulator;
    }
    
    /**
     * @inheritDoc
     * @see \vDesk\Struct\Collections\IEnumerable::Any()
     */
    public function Any(callable $Predicate): bool {
        foreach($this->Elements as $Index => $Value) {
            if($Predicate($Value, $Index, $this)) {
                return true;
            }
        }
        return false;
    }
    
}

