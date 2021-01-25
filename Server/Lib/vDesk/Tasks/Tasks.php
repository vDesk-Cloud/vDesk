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
    private Collection $Tasks;
    
    /**
     * The current running Tasks of the Machine.
     *
     * @var \vDesk\Struct\Collections\Queue
     */
    private Queue $Running;
    
    /**
     * @inheritDoc
     */
    public function Start(): void {
        $this->Running = new Queue();
        $this->Tasks   = new Collection();
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
            $Task = new $Class(\time());
            if(!$Task instanceof Task) {
                continue;
            }
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
        $TimeStamp = \time();
        
        //Get pending Tasks.
        foreach($this->Tasks->Filter(fn(Task $Task): bool => $Task->Next <= $TimeStamp) as $Task) {
            $this->Running->Enqueue($Task);
        }
        
        //Schedule Tasks.
        foreach($this->Running as $Task) {
            if($Task->Schedule()) {
                $this->Running->Enqueue($Task);
            }
        }
        
        //Sleep until next schedule.
        \sleep(
            \max(
                $this->Tasks->Reduce(
                    static fn(int $Previous, Task $Current): int => \min($Current->Next, $Previous),
                    $this->Tasks[0]->Next
                ) - $TimeStamp,
                1
            )
        );
    }
}