<?php
declare(strict_types=1);

namespace vDesk\Machines;

use vDesk\Configuration\Settings;
use vDesk\DataProvider\Expression;
use vDesk\IO\Path;
use vDesk\Struct\Collections\Collection;
use vDesk\Struct\Collections\Queue;
use vDesk\Tasks\Task;
use vDesk\Utils\Log;

/**
 * Machine that schedules Tasks.
 *
 * @package vDesk\Machines
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Tasks extends Machine {

    /**
     * The Tasks of the Machine.
     *
     * @var \vDesk\Struct\Collections\Collection
     */
    protected Collection $Tasks;

    /**
     * The current running Tasks of the Machine.
     *
     * @var \vDesk\Struct\Collections\Queue
     */
    protected Queue $Running;

    /**
     * @inheritDoc
     */
    public function Start(): void {
        $this->Running = new Queue();
        $this->Tasks   = new Collection();
        $TimeStamp = \microtime(true);
        foreach(
            Expression::Select("File", "Name")
                      ->From("Archive.Elements")
                      ->Where(
                          [
                              "Parent"    => Settings::$Local["Tasks"]["Directory"],
                              "Extension" => "php"
                          ]
                      )
            as $Task
        ) {
            include Settings::$Local["Archive"]["Directory"] . Path::Separator . $Task["File"];
            $Class = "vDesk\\Tasks\\{$Task["Name"]}";
            if(!\class_exists($Class)) {
                continue;
            }
            $Task = new $Class();
            if(!$Task instanceof Task) {
                continue;
            }
            $Task->Tasks = $this;
            $Task->TimeStamp = $TimeStamp;
            $Task->Start();
            $this->Tasks->Add($Task);
            $this->Running->Enqueue($Task);
        }
        if($this->Tasks->Count === 0) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to start Machine without any Tasks installed!");
            $this->Stop(1);
        }
    }

    /**
     * @inheritDoc
     */
    public function Run(): void {
        $Start = \microtime(true);

        //Get pending Tasks.
        /** @var \vDesk\Tasks\Task $Task */
        foreach($this->Tasks->Filter(static fn(Task $Task): bool => $Task->TimeStamp <= $Start) as $Task) {
            $this->Running->Enqueue($Task);
        }

        //Schedule Tasks.
        foreach($this->Running as $Task) {
            if($Task->Schedule()) {
                $this->Running->Enqueue($Task);
            }
        }

        //Get next estimated schedule time.
        $Next = $this->Tasks->Reduce(static fn(float $Previous, Task $Current): float => \min($Current->TimeStamp, $Previous), $this->Tasks[0]->TimeStamp);

        //Skip idle on overtime.
        $Stop = \microtime(true);
        if($Next <= $Stop) {
            return;
        }

        //Calculate idle time.
        $Estimated = $Next - $Stop;
        $Seconds      = (int)$Estimated;
        $Microseconds = (int)(\round($Estimated - $Seconds, 6) * 1000000);

        //Sleep until next schedule.
        \usleep($Microseconds);
        \sleep($Seconds);
    }

    /**
     * Adds a new Task to the scheduler.
     *
     * @param \vDesk\Tasks\Task $Task The Task to add for schedule.
     */
    public function Add(Task $Task): void {
        $Task->Tasks = $this;
        $Task->TimeStamp = \microtime(true);
        $Task->Start();
        $this->Tasks->Add($Task);
    }

    /**
     * Adds a new Task to the queue of the scheduler.
     *
     * @param \vDesk\Tasks\Task $Task The Task to add for schedule.
     */
    public function Schedule(Task $Task): void {
        $this->Add($Task);
        $this->Running->Enqueue($Task);
    }

    /**
     * Adds a new Task to the queue of the scheduler.
     *
     * @param \vDesk\Tasks\Task $Task The Task to add for schedule.
     */
    public function Remove(Task $Task): void {
        $Task->Stop(1);
        $this->Tasks->Remove($Task);
    }

    /**
     * @inheritDoc
     */
    public function Suspend(): void {
        foreach($this->Tasks as $Task) {
            $Task->Suspend();
        }
    }

    /**
     * @inheritDoc
     */
    public function Resume(): void {
        foreach($this->Tasks as $Task) {
            $Task->Resume();
        }
    }

    /**
     * @inheritDoc
     */
    public function Stop(int $Code = 0): void {
        foreach($this->Tasks as $Task) {
            $Task->Stop($Code);
        }
        parent::Stop($Code);
    }
}