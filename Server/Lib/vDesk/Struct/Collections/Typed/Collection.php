<?php
declare(strict_types=1);

namespace vDesk\Struct\Collections\Typed;

use vDesk\Struct\Collections\ICollection;
use vDesk\Struct\Collections\IndexOutOfRangeException;
use vDesk\Struct\InvalidOperationException;
use vDesk\Struct\Properties;
use vDesk\Struct\Type;

/**
 * Represents a statically typed iterable list of elements.
 *
 * @property-read int $Count Gets the amount of elements in the Collection.
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Collection implements ICollection {
    
    use Properties;
    
    /**
     * The Type of the Collection.
     */
    public const Type = Type::Mixed;
    
    /**
     * The elements of the Collection.
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
        foreach($Elements as $Element){
            $this->Add($Element);
        }
    }
    
    /**
     * Determines whether a passed argument or returned value is of the specified type of the Collection.
     *
     * @param mixed $Argument The argument to validate against the specified type of the Collection.
     *
     * @return bool True if the passed argument matches the specified type of the Collection; otherwise, false.
     */
    public static function IsValid(mixed &$Argument): bool {
        return static::Type === Type::Of($Argument);
    }
    
    /**
     * Creates a new TypeError indicating a wrong argument type.
     *
     * @param int    $ArgumentPosition The position of the argument which type doesn't match.
     * @param string $Method           The method which has been called within the type mismatch has occurred.
     * @param mixed  $Argument         The mismatching argument.
     *
     * @return \TypeError A pre-filled TypeError indicating a wrong argument type.
     */
    protected static function TypeError(int $ArgumentPosition, string $Method, mixed &$Argument): \TypeError {
        return Type::IsScalar($Argument)
            ? new \TypeError("Argument {$ArgumentPosition} passed to {$Method} must be of the type " . static::Type . ", " . Type::Of($Argument) . " given")
            : new \TypeError("Argument {$ArgumentPosition} passed to {$Method} must be an instance of " . static::Type . ", instance of " . Type::Of($Argument) . " given");
    }

    //Implementation of \vDesk\Struct\Collections\ICollection.
    /** @inheritDoc */
    public function Add(mixed $Element): void {
        if(!self::IsValid($Element)) {
            throw self::TypeError(1, __METHOD__, $Element);
        }
        $this->Elements[] = $Element;
    }

    /**
     * @inheritDoc
     * @throws \vDesk\Struct\Collections\IndexOutOfRangeException Thrown if the specified index doesn't exist.
     */
    public function Insert(int $Index, mixed $Element): void {
        if(!self::IsValid($Element)) {
            throw self::TypeError(1, __METHOD__, $Element);
        }
        if(
            $Index < 0
            || $Index > ($Count = $this->Count())
            || !$this->offsetExists($Index)
        ) {
            throw new IndexOutOfRangeException("Undefined index at " . __CLASS__ . "[$Index].");
        }

        if($Index === $Count) {
            $this->Add($Element);
        } else {
            $Temp             = \array_splice($this->Elements, $Index);
            $this->Elements[] = $Element;
            \array_merge($this->Elements, $Temp);
            $this->Elements = \array_values($this->Elements);
        }
    }

    /** @inheritDoc */
    public function InsertAfter(int $Index, mixed $Element): void {
        $this->Insert(++$Index, $Element);
    }
    
    /** @inheritDoc */
    public function Replace(mixed $Element, mixed $Replacement): void {
        if(!self::IsValid($Element)) {
            throw self::TypeError(1, __METHOD__, $Element);
        }
        if(!self::IsValid($Replacement)) {
            throw self::TypeError(2, __METHOD__, $Replacement);
        }
        if(~($iIndex = $this->IndexOf($Element))) {
            $this->Elements[$iIndex] = $Replacement;
        }
    }

    /**
     * @inheritDoc
     * @throws \vDesk\Struct\Collections\IndexOutOfRangeException Thrown if the specified index doesn't exist.
     */
    public function ReplaceAt(int $Index, mixed $Element): mixed {
        if(!self::IsValid($Element)) {
            throw self::TypeError(2, __METHOD__, $Element);
        }
        if(!$this->offsetExists($Index)) {
            throw new IndexOutOfRangeException("Undefined index at " . __CLASS__ . "[$Index].");
        }
        $Previous = $this->Elements[$Index];
        $this->Elements[$Index] = $Element;
        return $Previous;
    }

    /** @inheritDoc */
    public function Remove(mixed $Element): mixed {
        if(!self::IsValid($Element)) {
            throw self::TypeError(1, __METHOD__, $Element);
        }
        $Value          = \array_splice($this->Elements, $this->IndexOf($Element), 1)[0];
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
        if(!self::IsValid($Element)) {
            throw self::TypeError(1, __METHOD__, $Element);
        }
        foreach($this->Elements as $Index => $InternalElement) {
            if($Element === $InternalElement) {
                return $Index;
            }
        }
        return -1;
    }

    /** @inheritDoc */
    public function Contains(mixed $Element): bool {
        if(!self::IsValid($Element)) {
            throw self::TypeError(1, __METHOD__, $Element);
        }
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
        return \array_slice($this->Elements, $From ?? 0, ($To ?? $this->Count) - $From);
    }

    //Implementation of \vDesk\Struct\Collections\IEnumerable.
    /** @inheritDoc */
    public function Count(): int {
        return \count($this->Elements);
    }

    /** @inheritDoc */
    public function First(bool $Remove = false): mixed {
        if($Remove){
            return \array_shift($this->Elements);
        }
        return \reset($this->Elements) ?: null;
    }

    /** @inheritDoc */
    public function Last(bool $Remove = false): mixed {
        if($Remove){
            return \array_pop($this->Elements);
        }
        return \end($this->Elements) ?: null;
    }

    /** @inheritDoc */
    public function Reverse(): static {
        return new static(\array_reverse($this->Elements));
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
    public function Any(callable $Predicate): bool {
        foreach($this as $Index => $Value) {
            if($Predicate($Value, $Index, $this)) {
                return true;
            }
        }
        return false;
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
    public function Clear(): void {
        \array_splice($this->Elements, 0);
    }

    //Implementation of \vDesk\Struct\Collections\ArrayAccess.
    /** @see \ArrayAccess::offsetExists() */
    public function offsetExists($Index): bool {
        if(!\is_int($Index)) {
            throw new \TypeError("Argument 1 passed to " . __METHOD__ . " must be of the type int, " . Type::Of($Index) . " given");
        }
        return isset($this->Elements[$Index]);
    }

    /** @see \ArrayAccess::offsetUnset() */
    public function offsetUnset($Index): void {
        throw new InvalidOperationException("Cannot unset element at index " . __CLASS__ . "[$Index]. Use " . __CLASS__ . "::RemoveAt($Index) instead.");
    }
    
    /** @see \ArrayAccess::offsetGet() */
    public function offsetGet($Index) {
        if(!$this->offsetExists($Index)) {
            throw new IndexOutOfRangeException("Undefined index at " . __CLASS__ . "[$Index].");
        }
        return $this->Elements[$Index];
    }

    /** @see \ArrayAccess::offsetSet() */
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
    public function current() {
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