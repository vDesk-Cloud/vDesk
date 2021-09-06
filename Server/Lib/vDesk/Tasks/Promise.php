<?php
declare(strict_types=1);

namespace vDesk\Tasks;

use vDesk\Utils\Log;

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
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function Start(): void {
        parent::Start();
        $this->Task->Tasks = $this->Tasks;
        $this->Task->Start();
    }

    /**
     * @inheritDoc
     */
    public function Run(): \Generator {
        $Value  = null;
        $Reject = $this->Reject;
        try {
            foreach($this->Task->Run() as $Key => $Value) {
                if($Key === self::Resolve) {
                    break;
                }
                if($Key === self::Reject) {
                    $Reject($Value);
                    return;
                }
                Log::Debug(__METHOD__, "Pending");
                yield;
            }
        } catch(\Throwable $Exception) {
            Log::Error(__METHOD__, "Rejecting {$Exception->getMessage()}");
            $Reject($Exception);
            return;
        }

        Log::Debug(__METHOD__, "Resolving");
        $Resolve = $this->Resolve;
        $Resolve($Value);
        yield;
        $this->Tasks->Remove($this);
        yield;

    }

    /**
     * Applies a callback to execute if the Promise has been rejected.
     *
     * @param callable $Predicate
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
     * @param callable $Predicate
     *
     * @return static The current instance for further chaining.
     */
    public function Reject(callable $Predicate): static {
        $this->Reject = $Predicate;
        return $this;
    }
}