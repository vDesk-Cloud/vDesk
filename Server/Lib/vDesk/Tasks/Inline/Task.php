<?php
declare(strict_types=1);

namespace vDesk\Tasks\Inline;

/**
 * Class for scheduling inline Generators.
 *
 * @package vDesk\Tasks
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Task extends \vDesk\Tasks\Task {

    /**
     * Per second interval.
     */
    public const Seconds = 1;

    /**
     * Initializes a new instance of the Task class.
     *
     * @param \Generator|\Closure $Delegate Initializes the Task with the specified delegated Generator.
     */
    public function __construct(private \Generator|\Closure $Delegate) {
        if($Delegate instanceof \Closure){
            $this->Delegate = $Delegate();
        }
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function Run(): \Generator {
        while($this->Delegate->valid()) {
            yield from $this->Delegate;
        }
        $this->Tasks->Remove($this);
    }

}