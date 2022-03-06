<?php
declare(strict_types=1);

namespace vDesk\Struct\Collections;

use vDesk\Struct\InvalidOperationException;
use vDesk\Struct\Properties;
use vDesk\Struct\Type;

/**
 * Represents a generic iterable list of objects.
 *
 * @property-read int $Count Gets the amount of elements in the Collection.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Collection implements ICollection {

    use Properties;

    /**
     * The elements of the Collection.
     *
     * @var array
     */
    protected array $Elements = [];

    /**
     * Initializes a new instance of the Collection class.
     *
     * @param iterable $Elements Initializes the Collection with the specified set of elements.
     */
    public function __construct(iterable $Elements = []) {
        $this->AddProperty("Count", [\Get => fn(): int => \count($this->Elements)]);
        foreach($Elements as $Element) {
            $this->Add($Element);
        }
    }

    //Implementation of \vDesk\Struct\Collections\ICollection.
    /** @inheritDoc */
    public function Add(mixed $Element): void {
        $this->Elements[] = $Element;
    }

    /**
     * @inheritDoc
     * @throws \vDesk\Struct\Collections\IndexOutOfRangeException Thrown if the specified index doesn't exist.
     */
    public function Insert(int $Index, mixed $Element): void {
        if($Index < 0 || $Index > ($iCount = $this->Count())) {
            throw new IndexOutOfRangeException("Undefined index at " . __CLASS__ . "[$Index].");
        }

        if($Index === $iCount) {
            $this->Add($Element);
        } else {
            $Temp             = \array_splice($this->Elements, $Index);
            $this->Elements[] = $Element;
            foreach($Temp as $Element) {
                $this->Elements[] = $Element;
            }
        }
    }

    /** @inheritDoc */
    public function InsertAfter(int $Index, mixed $Element): void {
        $this->Insert(++$Index, $Element);
    }

    /** @inheritDoc */
    public function Replace(mixed $Element, mixed $Replacement): void {
        if(~($iIndex = $this->IndexOf($Element))) {
            $this->Elements[$iIndex] = $Replacement;
        }
    }

    /**
     * @inheritDoc
     * @throws \vDesk\Struct\Collections\IndexOutOfRangeException Thrown if the specified index doesn't exist.
     */
    public function ReplaceAt(int $Index, mixed $Element): mixed {
        if(!$this->offsetExists($Index)) {
            throw new IndexOutOfRangeException("Undefined index at " . __CLASS__ . "[$Index].");
        }
        $Previous               = $this->Elements[$Index];
        $this->Elements[$Index] = $Element;
        return $Previous;
    }

    /** @inheritDoc */
    public function Remove(mixed $Element): mixed {
        $Value          = \array_splice($this->Elements, $this->IndexOf($Element), 1)[0] ?? null;
        $this->Elements = \array_values($this->Elements);
        return $Value;
    }

    /** @inheritDoc */
    public function RemoveAt(int $Index): mixed {
        if(isset($this->Elements[$Index])) {
            $Value          = \array_splice($this->Elements, $Index, 1)[0];
            $this->Elements = \array_values($this->Elements);
            return $Value;
        }
        return null;
    }

    /** @inheritDoc */
    public function IndexOf(mixed $Element): int {
        foreach($this->Elements as $Index => $InternalElement) {
            if($Element === $InternalElement) {
                return $Index;
            }
        }
        return -1;
    }

    /** @inheritDoc */
    public function Contains(mixed $Element): bool {
        return \in_array($Element, $this->Elements);
    }

    /** @inheritDoc */
    public function Merge(ICollection $Collection): void {
        foreach($Collection as $Item) {
            $this->Add($Item);
        }
    }

    /** @inheritDoc */
    public function ToArray(int $From = null, int $To = null): array {
        return \array_slice($this->Elements, $From ?? 0, ($To !== null) ? ($To - $From) : $this->Count);
    }

    //Implementation of \vDesk\Struct\Collections\IEnumerable.
    /** @inheritDoc */
    public function Count(): int {
        return \count($this->Elements);
    }

    /** @inheritDoc */
    public function Find(callable $Predicate): mixed {
        foreach($this as $Index => $Value) {
            if($Predicate($Value, $Index, $this)) {
                return $Value;
            }
        }
        return null;
    }

    /** @inheritDoc */
    public function Sort(callable $Predicate): static {
        $Sorted = $this->ToArray();
        \usort($Sorted, $Predicate);
        return new static($Sorted);
    }

    /** @inheritDoc */
    public function Filter(callable $Predicate): static {
        $Collection = new static();
        foreach($this as $Index => $Value) {
            if($Predicate($Value, $Index, $this)) {
                $Collection->Add($Value);
            }
        }
        return $Collection;
    }

    /** @inheritDoc */
    public function Map(callable $Predicate): static {
        $Collection = new static();
        foreach($this as $Index => $Value) {
            $Collection->Add($Predicate($Value, $Index, $this));
        }
        return $Collection;
    }

    /** @inheritDoc */
    public function Reduce(callable $Predicate, $InitialValue = null): mixed {
        $Accumulator = $InitialValue ?? \reset($this->Elements);
        foreach($this as $Index => $Value) {
            $Accumulator = $Predicate($Accumulator, $Value, $Index, $this);
        }
        return $Accumulator;
    }

    /** @inheritDoc */
    public function Every(callable $Predicate): bool {
        foreach($this as $Index => $Value) {
            if(!$Predicate($Value, $Index, $this)) {
                return false;
            }
        }
        return true;
    }

    /** @inheritDoc */
    public function Any(callable $Predicate): bool {
        foreach($this as $Index => $Value) {
            if($Predicate($Value, $Index, $this)) {
                return true;
            }
        }
        return false;
    }

    /** @inheritDoc */
    public function Clear(): void {
        $this->Elements = [];
    }

    //Implementation of \ArrayAccess.
    /**
     * @throws \TypeError Thrown if the specified index is not an integer.
     * @see \ArrayAccess::offsetExists()
     */
    public function offsetExists($Index): bool {
        if(!\is_int($Index)) {
            throw new \TypeError("Argument 1 passed to " . __METHOD__ . " must be of the type int, " . Type::Of($Index) . " given");
        }
        return isset($this->Elements[$Index]);
    }

    /**
     * @throws \vDesk\Struct\InvalidOperationException Thrown if an element is being deleted using unset($Index).
     * @see \ArrayAccess::offsetUnset()
     */
    public function offsetUnset($Index): void {
        throw new InvalidOperationException("Cannot unset element at index " . __CLASS__ . "[$Index]. Use " . __CLASS__ . "::RemoveAt($Index) instead.");
    }

    /**
     * @throws \vDesk\Struct\Collections\IndexOutOfRangeException Thrown if the specified index doesn't exist.
     * @see \ArrayAccess::offsetGet()
     */
    public function offsetGet($Index) {
        if(!$this->offsetExists($Index)) {
            throw new IndexOutOfRangeException("Undefined index at " . __CLASS__ . "[$Index].");
        }
        return $this->Elements[$Index];
    }

    /**
     * @throws \vDesk\Struct\Collections\IndexOutOfRangeException Thrown if the specified index doesn't exist.
     * @see \ArrayAccess::offsetSet()
     */
    public function offsetSet($Index, $Value): void {
        // Check if an index has been specified.
        if($Index !== null) {
            // Check if the index is in range.
            if(!$this->offsetExists($Index)) {
                throw new IndexOutOfRangeException("Undefined index at " . __CLASS__ . "[$Index].");
            }
            $this->Replace($Index, $Value);
        } else {
            $this->Add($Value);
        }
    }

    //Implementation of \Iterator.
    /** @see \Iterator::rewind() */
    public function rewind(): void {
        \reset($this->Elements);
    }

    /** @see \Iterator::current() */
    public function current(): mixed {
        return \current($this->Elements);
    }

    /** @see \Iterator::key() */
    public function key(): int {
        return \key($this->Elements);
    }

    /** @see \Iterator::next() */
    public function next(): void {
        \next($this->Elements);
    }

    /** @see \Iterator::valid() */
    public function valid(): bool {
        return \key($this->Elements) !== null;
    }
}
