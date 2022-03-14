<?php
declare(strict_types=1);

namespace Modules;

use vDesk\Calendar\Events;
use vDesk\Calendar\Event;
use vDesk\Calendar\Event\Created;
use vDesk\Calendar\Event\Deleted;
use vDesk\Calendar\Event\Updated;
use vDesk\DataProvider\Expression;
use vDesk\DataProvider\Expression\Functions;
use vDesk\Modules\Command;
use vDesk\Modules\Module;
use vDesk\Search\Results;
use vDesk\Search\ISearch;
use vDesk\Search\Result;
use vDesk\Security\AccessControlList;
use vDesk\Security\AccessControlList\Entry;
use vDesk\Security\UnauthorizedAccessException;
use vDesk\Security\User;
use vDesk\Utils\Log;

/**
 * Calendar Module class.
 *
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Calendar extends Module implements ISearch {

    /**
     * Gets the data of an Event.
     *
     * @param null|int $ID The ID of the Event to get.
     *
     * @return \vDesk\Calendar\Event The model-representation of the Event.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to read the specified Event.
     */
    public static function GetEvent(int $ID = null): Event {
        $Event = (new Event($ID ?? Command::$Parameters["ID"]))->Fill();
        if(!$Event->AccessControlList->Read) {
            throw new UnauthorizedAccessException();
        }
        return $Event;
    }

    /**
     * Returns all accessible events within a given time-period.
     *
     * @param null|\DateTime $From The start-date of the time-range.
     * @param null|\DateTime $To   The end-date of the time-range.
     *
     * @return \vDesk\Calendar\Events All found events the user is allowed to see.
     */
    public static function GetEvents(\DateTime $From = null, \DateTime $To = null): Events {
        return Events::Between($From ?? Command::$Parameters["From"], $To ?? Command::$Parameters["To"]);
    }

    /**
     * Gets all participants of an {@link \vDesk\Calendar\Event} Event.
     *
     * @param null|int $ID The ID of the Event.
     *
     * @return \vDesk\Calendar\Event\Participants The {@link \vDesk\Calendar\Event\Participant} Participants of the Event.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to read the specified Event.
     */
    public static function GetParticipants(int $ID = null): Event\Participants {
        $Event = new Event($ID ?? Command::$Parameters["ID"]);
        if(!$Event->AccessControlList->Read) {
            Log::Error(__METHOD__, User::$Current->Name . " tried to get Participants of an Event without having permissions.");
            throw new UnauthorizedAccessException();
        }
        return $Event->Participants->Fill();
    }

    /**
     * Creates a new Event.
     * Triggers the {@link \vDesk\Calendar\Created}-Event for the created Event.
     *
     * @param \DateTime|null            $Start          The date and time when the event begins.
     * @param \DateTime|null            $End            The date and time when the event ends.
     * @param bool|null                 $FullTime       Determines whether the Event spans over the whole day.
     * @param int|null                  $RepeatAmount   The amount of times the Event re-occurs.
     * @param int|null                  $RepeatInterval The interval in days the Event re-occurs.
     * @param \vDesk\Security\User|null $Owner          The owner of the Event.
     * @param string|null               $Title          The title of the Event.
     * @param string|null               $Color          The color of the Event.
     * @param string|null               $Content        The content of the Event.
     * @param array|null                $Participants   The Participants of the event.
     *
     * @return \vDesk\Calendar\Event The created Event.
     */
    public static function CreateEvent(
        \DateTime $Start = null,
        \DateTime $End = null,
        bool      $FullTime = null,
        int       $RepeatAmount = null,
        int       $RepeatInterval = null,
        User      $Owner = null,
        string    $Title = null,
        string    $Color = null,
        string    $Content = null,
        array     $Participants = null
    ): Event {
        $Event = new Event(
            null,
            $Owner ?? User::$Current,
            $Start ?? Command::$Parameters["Start"],
            $End ?? Command::$Parameters["End"],
            $FullTime ?? Command::$Parameters["FullTime"],
            $RepeatAmount ?? Command::$Parameters["RepeatAmount"],
            $RepeatInterval ?? Command::$Parameters["RepeatInterval"],
            $Title ?? Command::$Parameters["Title"],
            $Color ?? Command::$Parameters["Color"],
            $Content ?? Command::$Parameters["Content"],
            null,
            new AccessControlList([
                Entry::FromUser(),
                Entry::FromGroup(null, false, false, false),
                Entry::FromUser($Owner ?? User::$Current)
            ])
        );
        $Event->Save();
        if(\count($Participants ?? Command::$Parameters["Participants"] ?? []) > 0) {
            self::AddParticipants($Event->ID, $Participants);
        }
        (new Created($Event))->Dispatch();
        return $Event;
    }

    /**
     * Updates an Event.
     * Triggers the {@link \vDesk\Calendar\Updated}-Event for the updated {@link \vDesk\Calendar\Event}.
     *
     * @param int|null       $ID             The ID of the Event to update.
     * @param \DateTime|null $Start          The date and time when the Event to update begins.
     * @param \DateTime|null $End            The date and time when the Event to update ends.
     * @param bool|null      $FullTime       Determines whether the Event spans over the whole day.
     * @param int|null       $RepeatAmount   The amount of times the Event re-occurrs.
     * @param int|null       $RepeatInterval The interval in days the Event re-occurrs.
     * @param string|null    $Title          The title of the Event.
     * @param string|null    $Color          The color of the Event.
     * @param string|null    $Content        The content of the Event.
     *
     * @return \vDesk\Calendar\Event The updated Event.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to update the specified Event.
     */
    public static function UpdateEvent(
        int       $ID = null,
        \DateTime $Start = null,
        \DateTime $End = null,
        bool      $FullTime = null,
        int       $RepeatAmount = null,
        int       $RepeatInterval = null,
        string    $Title = null,
        string    $Color = null,
        string    $Content = null
    ): Event {
        $Event = (new Event($ID ?? Command::$Parameters["ID"]))->Fill();
        if(!$Event->AccessControlList->Write) {
            Log::Error(__METHOD__, User::$Current->Name . " tried to update Event without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Event->Start          = $Start ?? Command::$Parameters["Start"];
        $Event->End            = $End ?? Command::$Parameters["End"];
        $Event->FullTime       = $FullTime ?? Command::$Parameters["FullTime"];
        $Event->RepeatAmount   = $RepeatAmount ?? Command::$Parameters["RepeatAmount"];
        $Event->RepeatInterval = $RepeatInterval ?? Command::$Parameters["RepeatInterval"];
        $Event->Title          = $Title ?? Command::$Parameters["Title"];
        $Event->Color          = $Color ?? Command::$Parameters["Color"];
        $Event->Content        = $Content ?? Command::$Parameters["Content"];
        $Event->Save();
        (new Updated($Event))->Dispatch();
        return $Event;
    }

    /**
     * Updates the start- and end date and time of an existing {@link \vDesk\Calendar\Event}.
     * Triggers the {@link \vDesk\Calendar\Updated}-Event for the updated {@link \vDesk\Calendar\Event}.
     *
     * @param null|int       $ID    The ID of the Event to update.
     * @param null|\DateTime $Start The new start date of the Event.
     * @param null|\DateTime $End   The new end date of the Event.
     *
     * @return bool True if the Event has been successfully updated.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to update the specified Event.
     */
    public static function UpdateEventDate(int $ID = null, \DateTime $Start = null, \DateTime $End = null): bool {
        $Event = (new Event($ID ?? Command::$Parameters["ID"]))->Fill();
        if(!$Event->AccessControlList->Write) {
            Log::Error(__METHOD__, User::$Current->Name . " tried to update Event without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Event->Start = $Start ?? Command::$Parameters["Start"];
        $Event->End   = $End ?? Command::$Parameters["End"];
        $Event->Save();
        (new Updated($Event))->Dispatch();
        return true;
    }

    /**
     * Deletes an Event.
     * Triggers the {@link \vDesk\Calendar\Event\Deleted}-Event for the deleted {@link \vDesk\Calendar\Event}.
     *
     * @param null|int $ID The ID of the Event to delete.
     *
     * @return bool True if the Event has been successfully deleted.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to delete the specified Event.
     */
    public static function DeleteEvent(int $ID = null): bool {
        $Event = new Event($ID ?? Command::$Parameters["ID"]);
        if(!$Event->AccessControlList->Delete) {
            Log::Error(__METHOD__, User::$Current->Name . " tried to delete Event [{$Event->ID}]({$Event->Title}) without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Event->Delete();
        (new Deleted($Event))->Dispatch();
        return true;
    }

    /**
     * Adds a set of Participants to an Event.
     *
     * @param null|int   $ID    The ID of the {@link \vDesk\Calendar\Event}.
     * @param null|array $Users An array containing the IDs of the Users to add.
     *
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to update the specified Event.
     */
    public static function AddParticipants(int $ID = null, array $Users = null): void {
        $Event = new Event($ID ?? Command::$Parameters["ID"]);
        if(!$Event->AccessControlList->Write) {
            Log::Error(__METHOD__, User::$Current->Name . " tried to add Participants without having permissions.");
            throw new UnauthorizedAccessException();
        }
        //Fill collections first, required for check of duplicate entries.
        $Event->Participants->Fill();
        $Event->AccessControlList->Fill();
        //Loop through Users.
        foreach($Users ?? Command::$Parameters["Users"] as $User) {
            $Participant         = new Event\Participant();
            $Participant->User   = new User($User);
            $Participant->Status = Event\Participation::NotResponed;
            $Event->Participants->Add($Participant);
            /**
             * @var \vDesk\Security\AccessControlList\Entry $Entry Eventually existing ACL-Entry.
             */
            //Check if an entry already exists
            $Entry = $Event->AccessControlList->Find(static fn(Entry $Entry): bool => $Entry->User->ID === $User);

            if($Entry !== null) {
                //If true, update the existing element with read permissions.
                $Entry->Read = true;
            } else {
                //Else create a new one with read permissions.
                $Event->AccessControlList->Add(Entry::FromUser(new User($User), true, false, false));
            }
        }
        $Event->Save();
    }

    /**
     * Searches the Calendar for Events with a similar title.
     *
     * @param string      $Value The title to search for.
     *
     * @param string|null $Filter
     *
     * @return \vDesk\Search\Results The found Events.
     */
    public static function Search(string $Value, string $Filter = null): Results {
        $Results = new Results();
        foreach(
            Expression::Select("*")
                      ->From("Calendar.Events")
                      ->Where(["Title" => ["LIKE" => "%{$Value}%"]])
            as
            $Row
        ) {
            $Event = new Event(
                (int)$Row["ID"],
                new User((int)$Row["Owner"]),
                new \DateTime($Row["Start"]),
                new \DateTime($Row["End"]),
                (bool)$Row["FullTime"],
                (int)$Row["RepeatAmount"],
                (int)$Row["RepeatInterval"],
                $Row["Title"],
                $Row["Color"],
                $Row["Content"],
                null,
                new AccessControlList([], (int)$Row["AccessControlList"])
            );
            if($Event->AccessControlList->Read) {
                $Results->Add(new Result($Event->Title, "Event", $Event->ToDataView()));
            }
        }
        return $Results;
    }

    /**
     * Gets the status information of the Calendar.
     *
     * @return null|array An array containing the amount of Calendar Events.
     */
    public static function Status(): ?array {
        return [
            "EventCount" => Expression::Select(Functions::Count("*"))
                                      ->From("Calendar.Events")()
        ];
    }

}
