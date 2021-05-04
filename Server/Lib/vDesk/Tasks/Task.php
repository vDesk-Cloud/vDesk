<?php
declare(strict_types=1);

namespace vDesk\Tasks;

use vDesk\Machines\Tasks;

/**
 * Abstract baseclass for Tasks.
 *
 * @package vDesk\Tasks
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
abstract class Task {
    
    /**
     * Per micro second interval.
     */
    public const MicroSeconds = 0;
    
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
     * @var float
     */
    public float $Next;
    
    private \Generator $Generator;
    
    /**
     * Initializes a new instance of the Task class.
     *
     * @param null|\vDesk\Machines\Tasks $Tasks Initializes the Task with the specified Task scheduler.
     */
    public function __construct(protected ?Tasks $Tasks = null) {
        $this->Generator = $this->Run();
    }
    
    /**
     * Starts the Task in a specified dispatcher.
     *
     * @param \vDesk\Machines\Tasks $Tasks Starts the Task with the specified Task scheduler.
     */
    public function Start(Tasks $Tasks): void {
        $this->Tasks = $Tasks;
        $this->Next  = static::Next(\microtime());
    }
    
    /**
     * Runs the Task.
     */
    abstract public function Run(): \Generator;
    
    
    /**
     * Suspends the Task.
     */
    public function Suspend(): void {
    }
    
    /**
     * Resumes the Task.
     */
    public function Resume(): void {
    }
    
    /**
     * Stops the Task.
     *
     * @param int $Code The stop code of the Task dispatcher.
     */
    public function Stop(int $Code): void {
        $this->Tasks->Tasks->Remove($this);
    }
    
    /**
     * Schedules the Tasks.
     *
     * @return bool A Generator that yields a value indicating whether the Task is running.
     */
    final public function Schedule(): bool {
        //Keep executing the Task if it's running.
        $this->Generator->next();
        if($this->Generator->valid()) {
            return true;
        }
        
        //Calculate next estimated schedule.
        $this->Next = static::Next($this->Next);
        
        //Run Task.
        $this->Generator = $this->Run();
        return false;
    }
    
    /**
     * Calculates the next estimated schedule timestamp in microseconds according a specified timestamp.
     *
     * @param float $Timestamp The timestamp in microseconds to calculate the next schedule from.
     *
     * @return float A float representing the next estimated schedule timestamp of the Task.
     */
    public static function Next(float $Timestamp): float {
        return $Timestamp
               + static::MicroSeconds
               + (static::Seconds * 1000000)
               + (static::Minutes * 60 * 1000000)
               + (static::Hours * 3600 * 1000000)
               + (static::Days * 86400 * 1000000)
               + (static::Weeks * 604800 * 1000000)
               + (static::Months * 2629746 * 1000000)
               + (static::Years * 31556952 * 1000000);
    }
    
}