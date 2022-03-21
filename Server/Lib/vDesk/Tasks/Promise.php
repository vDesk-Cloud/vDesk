<?php
declare(strict_types=1);

namespace vDesk\Tasks;

use vDesk\Machines\Tasks;

/**
 * Task based Promise for executing Tasks.
 *
 * @package vDesk\Tasks
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Promise extends Task {

    /**
     * Control value indicating the Promise to manually resolve.
     */
    public const Resolve = "Resolve";

    /**
     * Control value indicating the Promise to manually reject.
     */
    public const Reject = "Reject";

    /**
     * Initializes a new instance of the Promise class.
     *
     * @param \vDesk\Tasks\Task $Task    Initializes the Promise with the specified Task to resolve.
     * @param null|\Closure     $Resolve Initializes the Promise with the specified resolve callback.
     * @param null|\Closure     $Reject  Initializes the Promise with the specified reject callback.
     */
    public function __construct(public Task $Task, protected ?\Closure $Resolve = null, protected ?\Closure $Reject = null) {
    }

    /**
     * @inheritDoc
     */
    public function Start(Tasks $Tasks): void {
        parent::Start($Tasks);
        $this->Task->Start($Tasks);
    }

    /**
     * @inheritDoc
     */
    public function Run(): \Generator {
        $Key    = null;
        $Value  = null;
        try {
            foreach($this->Task->Run() as $Key => $Value) {
                if($Key === self::Resolve || $Key === self::Reject) {
                    break;
                }
                yield;
            }
        } catch(\Throwable $Exception) {
            ($this->Reject)($Exception);
            yield;
            $this->Tasks->Remove($this);
            return;
        }
        if($Key === self::Reject) {
            ($this->Reject)($Value);
        } else {
            ($this->Resolve)($Value);
        }
        yield;
        $this->Tasks->Remove($this);
        yield;
    }

    /**
     * Applies a callback to execute if the Promise has been rejected.
     *
     * @param callable $Predicate The predicate to call if the Promise has been resolved.
     *
     * @return static The current instance for further chaining.
     */
    public function Resolve(callable $Predicate): static {
        $this->Resolve = $Predicate;
        return $this;
    }

    /**
     * Applies a callback to execute if the Promise has been rejected.
     *
     * @param callable $Predicate The predicate to call if the Promise has been rejected.
     *
     * @return static The current instance for further chaining.
     */
    public function Reject(callable $Predicate): static {
        $this->Reject = $Predicate;
        return $this;
    }
}