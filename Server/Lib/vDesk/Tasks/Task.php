<?php
declare(strict_types=1);

namespace vDesk\Tasks;

/**
 * Abstract baseclass for Tasks.
 *
 * @package vDesk\Tasks
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Task {
    
    /**
     * Per second interval.
     */
    public const Seconds = 0;
    
    /**
     * Per minute interval.
     */
    public const Minutes = 5;
    
    /**
     * Per hour interval.
     */
    public const Hours = 0;
    
    /**
     * Per day interval.
     */
    public const Days = 0;
    
    /**
     * Per week interval.
     */
    public const Weeks = 0;
    
    /**
     * Per month interval.
     */
    public const Months = 0;
    
    /**
     * Per year interval.
     */
    public const Years = 0;
    
    /**
     * The calculated timestamp of the next schedule of the Task.
     *
     * @var int
     */
    public int $Next;
    
    private \Generator $Generator;
    
    /**
     * Start/Run/Stop?
     *
     * @param int $Previous
     */
    public function __construct(public int $Previous) {
        $this->Next();
        $this->Generator = $this->Run();
    }
    
    /**
     * Runs the Task.
     */
    abstract public function Run(): \Generator;
    
    /**
     * Schedules the Tasks.
     *
     * @return bool A Generator that yields a value indicating whether the Task is running.
     */
    final public function Schedule(): bool {
        $this->Generator->next();
        if($this->Generator->valid()) {
            return true;
        }
        $this->Previous = $this->Next;
        $this->Next();
        $this->Generator = $this->Run();
        return false;
    }
    
    /**
     */
    private function Next(): void {
        $this->Next = $this->Previous
            + static::Seconds
            + (static::Minutes * 60)
            + (static::Hours * 3600)
            + (static::Days * 86400)
            + (static::Weeks * 604800)
            + (static::Months * 2629746)
            + (static::Years * 31556952);
    }
    
}