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
    public const Minutes = 0;

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
     * The Generator yielding the steps of the Task.
     *
     * @var \Generator
     */
    private \Generator $Generator;

    /**
     * The schedule interval of the Task.
     *
     * @var float
     */
    public float $Interval = 0.0;

    /**
     * The parent Task scheduler of the Task.
     *
     * @var null|\vDesk\Machines\Tasks
     */
    public ?Tasks $Tasks = null;

    /**
     * The start/schedule timestamp of the Task.
     *
     * @var null|float
     */
    public ?float $TimeStamp = null;

    /**
     * Initializes a new instance of the Task class.
     */
    public function __construct() {
        $this->Generator = $this->Run();

        //Calculate schedule interval.
        $this->Interval =
            (static::MicroSeconds * 0.000001)
            + static::Seconds
            + (static::Minutes * 60)
            + (static::Hours * 3600)
            + (static::Days * 86400)
            + (static::Weeks * 604800)
            + (static::Months * 2629746)
            + (static::Years * 31556952);
    }

    /**
     * Starts the Task.
     */
    public function Start(): void {
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
        $this->TimeStamp += $this->Interval;

        //Run Task.
        $this->Generator = $this->Run();
        return false;
    }

}