<?php
declare(strict_types=1);

namespace vDesk\Calendar;

use vDesk\Calendar\Event\Participants;
use vDesk\DataProvider\Expression;
use vDesk\Data\IDataView;
use vDesk\Security\AccessControlList;
use vDesk\Security\User;
use vDesk\Struct\Collections\Typed\Collection;

/**
 * Represents a collection of {@link \vDesk\Calendar\Event} objects.
 *
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Events extends Collection implements IDataView {
    
    /**
     * The Type of the Events.
     */
    public const Type = Event::class;
    
    /**
     * Fetches a Collection yielding every {@link \vDesk\Calendar\Event} that occurs between two specified dates.
     *
     * @param \DateTime $From The start date of the period.
     * @param \DateTime $To   The end date of the period.
     *
     * @return \vDesk\Calendar\Events A collection that yields every Event that occurs between the specified dates.
     */
    public static function Between(\DateTime $From, \DateTime $To): Events {
        return new static(
            (static function() use ($From, $To): \Generator {
                foreach(
                    Expression::Select("*")
                              ->From("Calendar.Events")
                              ->Where(
                                  [
                                      "Start" => ["<" => $From],
                                      "End"   => [">" => $To]
                                  ],
                                  ["Start" => ["BETWEEN" => [$From, $To]]],
                                  ["End" => ["BETWEEN" => [$From, $To]]]
                              )
                    as
                    $Event
                ) {
                    $Event = new Event(
                        (int)$Event["ID"],
                        new User((int)$Event["Owner"]),
                        new \DateTime($Event["Start"]),
                        new \DateTime($Event["End"]),
                        (bool)$Event["FullTime"],
                        (int)$Event["RepeatInterval"],
                        (int)$Event["RepeatAmount"],
                        $Event["Title"],
                        $Event["Color"],
                        $Event["Content"],
                        null,
                        new AccessControlList([], (int)$Event["AccessControlList"])
                    );
                    if($Event->AccessControlList->Read) {
                        yield $Event;
                    }
                }
            })()
        );
    }
    
    /**
     * @inheritdoc
     */
    public function Find(callable $Predicate): ?Event {
        return parent::Find($Predicate);
    }
    
    /**
     * @inheritdoc
     */
    public function Remove($Element): Event {
        return parent::Remove($Element);
    }
    
    /**
     * @inheritdoc
     */
    public function RemoveAt(int $Index): Event {
        return parent::RemoveAt($Index);
    }
    
    /**
     * @inheritdoc
     */
    public function offsetGet($Index): Event {
        return parent::offsetGet($Index);
    }
    
    /**
     * Creates a Events from a specified data view.
     *
     * @param array $DataView The data to use to create a Events.
     *
     * @return \vDesk\Calendar\Events A Events created from the specified data view.
     */
    public static function FromDataView(mixed $DataView): Events {
        return new static(
            (static function() use ($DataView) {
                foreach($DataView as $aoData) {
                    yield Event::FromDataView($aoData);
                }
            })()
        );
    }
    
    /**
     * Creates a data view of the Events.
     *
     * @return array The data view representing the current state of the Events.
     */
    public function ToDataView(): array {
        return $this->Reduce(static function(array $Events, Event $Event): array {
            $Events[] = $Event->ToDataView();
            return $Events;
        },
            []);
    }
    
}
