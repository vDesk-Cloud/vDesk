<?php
declare(strict_types=1);

namespace vDesk\Events;

use vDesk\DataProvider\Expression;
use vDesk\Struct\Properties;

/**
 * Represents a provider that continously fetches dispatched events of an event-queue.
 *
 * @property int Interval Gets or sets the interval in seconds the EventProvider will check for new events.
 * @package vDesk\Events
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class EventProvider {

    use Properties;

    /**
     * The default interval in seconds the EventProvider will check for new events.
     */
    public const DefaultInterval = 10;

    /**
     * The interval in seconds the EventProvider will check for new occurred Events.
     *
     * @var int
     */
    public int $Interval = self::DefaultInterval;

    /**
     * Initializes a new instance of the EventProvider class.
     *
     * @param int $Interval Initializes the EventProvider with the specified interval in seconds.
     */
    public function __construct(int $Interval = self::DefaultInterval) {
        $this->Interval = $Interval;
    }

    /**
     * Fetches an recently dispatched event from the event-queue.
     *
     * @return \Generator The string-representation of the event, or an empty string if no event has occurred.
     */
    public function FetchEvents(): \Generator {

        while(true) {

            $TimeStamp = \time();
            //Fetch public events.
            foreach(
                Expression::Select("*")
                          ->From("Events.Public")
                          ->Where(["TimeStamp" => ["<=" => $TimeStamp]])
                as
                $Event
            ) {
                yield $Event["Name"] => $Event["Data"];
            }

            //Delete public events that occurred before the twice amount of interval seconds of the current time.
            Expression::Delete()
                      ->From("Events.Public")
                      ->Where(["TimeStamp" => ["<" => $TimeStamp - $this->Interval]])
                      ->Execute();

            //Fetch private events.
            foreach(
                Expression::Select("*")
                          ->From("Events.Private")
                          ->Where([
                              "TimeStamp" => ["<=" => $TimeStamp],
                              "Receiver"  => \vDesk::$User
                          ])
                as
                $Event
            ) {
                yield $Event["Name"] => $Event["Data"];
            }

            //Delete private events.
            Expression::Delete()
                      ->From("Events.Private")
                      ->Where([
                          "TimeStamp" => ["<=" => $TimeStamp],
                          "Receiver"  => \vDesk::$User
                      ])
                      ->Execute();

            \sleep($this->Interval);
        }
    }

}
