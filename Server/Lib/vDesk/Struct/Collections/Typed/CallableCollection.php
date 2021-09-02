<?php
declare(strict_types=1);

namespace vDesk\Struct\Collections\Typed;


/**
 * Represents a statically typed iterable list of callable values.
 *
 * @property-read int $Count Gets the amount of elements in the Collection<callable>.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class CallableCollection extends Collection {

    /**
     * The Type of the CallableCollection.
     */
    public const Type = \Closure::class;

    /**
     * @inheritdoc
     */
    public function Find(callable $Predicate): ?\Closure {
        return parent::Find($Predicate);
    }

    /**
     * @inheritdoc
     */
    public function Remove($Element): \Closure {
        return parent::Remove($Element);
    }

    /**
     * @inheritdoc
     */
    public function RemoveAt(int $Index): \Closure {
        return parent::RemoveAt($Index);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($Index): \Closure {
        return parent::offsetGet($Index);
    }
}