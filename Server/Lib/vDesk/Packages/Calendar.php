<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\Collation;
use vDesk\DataProvider\Type;
use vDesk\Locale;
use vDesk\Events;
use vDesk\Package;
use vDesk\Modules\Module\Command;
use vDesk\Modules\Module\Command\Parameter;
use vDesk\Security\AccessControlList;
use vDesk\Struct\Collections\Observable\Collection;

/**
 * Class Calendar represents ...
 *
 * @package vDesk\Packages
 * @author  Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
final class Calendar extends Package implements Locale\IPackage, Events\IPackage {
    
    /**
     * The name of the Package.
     */
    public const Name = "Calendar";
    
    /**
     * The version of the Package.
     */
    public const Version = "1.0.0";
    
    /**
     * The name of the Package.
     */
    public const Vendor = "Kerry <DevelopmentHero@gmail.com>";
    
    /**
     * The name of the Package.
     */
    public const Description = "Package providing functionality for organizing events and meetings.";
    
    /**
     * The dependencies of the Package.
     */
    public const Dependencies = [
        "Events"   => "1.0.0",
        "Locale"   => "1.0.0",
        "Security" => "1.0.0",
        "Search"   => "1.0.0"
    ];
    
    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Client => [
            self::Design  => [
                "vDesk/Calendar.css",
                "vDesk/Calendar"
            ],
            self::Modules => [
                "Calendar.js"
            ],
            self::Lib     => [
                "vDesk/Calendar.js",
                "vDesk/Calendar"
            ]
        ],
        self::Server => [
            self::Modules => [
                "Calendar.php"
            ],
            self::Lib     => [
                "vDesk/Calendar"
            ]
        ]
    ];
    
    /**
     * The eventlisteners of the Package.
     */
    public const Events = [
        "vDesk.Security.User.Deleted" => "/vDesk/Calendar/vDesk.Security.User.Deleted.php"
    ];
    
    /**
     * The translations of the Package.
     */
    public const Locale = [
        "DE" => [
            "Calendar" => [
                "April"          => "April",
                "AprilShort"     => "Apr",
                "August"         => "August",
                "AugustShort"    => "Aug",
                "Date"           => "Datum",
                "Day"            => "Tag",
                "December"       => "Dezember",
                "DecemberShort"  => "Dez",
                "EditEvent"      => "Termin bearbeiten",
                "End"            => "Ende",
                "Event"          => "Termin",
                "February"       => "Februar",
                "FebruaryShort"  => "Feb",
                "Friday"         => "Freitag",
                "FridayShort"    => "Fr",
                "FullTime"       => "Ganztägig",
                "GoTo"           => "Gehe zu",
                "GoToDate"       => "Gehe zu Datum",
                "January"        => "Januar",
                "JanuaryShort"   => "Jan",
                "July"           => "Juli",
                "JulyShort"      => "Jul",
                "June"           => "Juni",
                "JuneShort"      => "Jun",
                "March"          => "März",
                "MarchShort"     => "Mär",
                "May"            => "Mai",
                "MayShort"       => "Mai",
                "Meeting"        => "Besprechung",
                "Module"         => "Kalender",
                "Monday"         => "Montag",
                "MondayShort"    => "Mo",
                "Month"          => "Monat",
                "NewEvent"       => "Neuer Termin",
                "November"       => "November",
                "NovemberShort"  => "Nov",
                "October"        => "Oktober",
                "OctoberShort"   => "Okt",
                "Saturday"       => "Samstag",
                "SaturdayShort"  => "Sa",
                "September"      => "September",
                "SeptemberShort" => "Sep",
                "Start"          => "Beginn",
                "Sunday"         => "Sonntag",
                "SundayShort"    => "So",
                "Thursday"       => "Donnerstag",
                "ThursdayShort"  => "Do",
                "Today"          => "Heute",
                "Tuesday"        => "Dienstag",
                "TuesdayShort"   => "Di",
                "Wednesday"      => "Mittwoch",
                "WednesdayShort" => "Mi",
                "Year"           => "Jahr"
            ]
        ],
        "EN" => [
            "Calendar" => [
                "April"          => "April",
                "AprilShort"     => "Apr",
                "August"         => "August",
                "AugustShort"    => "Aug",
                "Date"           => "Date",
                "Day"            => "Day",
                "December"       => "December",
                "DecemberShort"  => "Dec",
                "EditEvent"      => "Edit event",
                "End"            => "End",
                "Event"          => "Event",
                "February"       => "February",
                "FebruaryShort"  => "Feb",
                "Friday"         => "Friday",
                "FridayShort"    => "Fri",
                "FullTime"       => "Full-time",
                "GoTo"           => "Go to",
                "GoToDate"       => "Go to date",
                "January"        => "January",
                "JanuaryShort"   => "Jan",
                "July"           => "July",
                "JulyShort"      => "Jul",
                "June"           => "June",
                "JuneShort"      => "Jun",
                "March"          => "March",
                "MarchShort"     => "Mar",
                "May"            => "May",
                "MayShort"       => "May",
                "Meeting"        => "Meeting",
                "Module"         => "Calendar",
                "Monday"         => "Monday",
                "MondayShort"    => "Mo",
                "Month"          => "Month",
                "NewEvent"       => "New event",
                "November"       => "November",
                "NovemberShort"  => "Nov",
                "October"        => "October",
                "OctoberShort"   => "Oct",
                "Saturday"       => "Saturday",
                "SaturdayShort"  => "Sat",
                "September"      => "September",
                "SeptemberShort" => "Sep",
                "Start"          => "Start",
                "Sunday"         => "Sunday",
                "SundayShort"    => "Sun",
                "Thursday"       => "Thursday",
                "ThursdayShort"  => "Thu",
                "Today"          => "Today",
                "Tuesday"        => "Tuesday",
                "TuesdayShort"   => "Tue",
                "Wednesday"      => "Wednesday",
                "WednesdayShort" => "Wed",
                "Year"           => "Year"
            ]
        ]
    ];
    
    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        
        Expression::Create()
                  ->Database("Calendar")
                  ->Execute();
        
        //Create tables.
        Expression::Create()
                  ->Table(
                      "Calendar.Events",
                      [
                          "ID"                => ["Type" => Type::BigInt | Type::Unsigned, "Autoincrement" => true],
                          "Owner"             => ["Type" => Type::BigInt | Type::Unsigned],
                          "Start"             => ["Type" => Type::DateTime],
                          "End"               => ["Type" => Type::DateTime],
                          "FullTime"          => ["Type" => Type::Boolean],
                          "RepeatAmount"      => ["Type" => Type::TinyInt | Type::Unsigned, "Size" => 3],
                          "RepeatInterval"    => ["Type" => Type::SmallInt | Type::Unsigned, "Size" => 5],
                          "Title"             => ["Type" => Type::TinyText, "Collation" => Collation::UTF8],
                          "Color"             => ["Type" => Type::VarChar, "Size" => 22, "Collation" => Collation::ASCII],
                          "Content"           => ["Type" => Type::Text, "Collation" => Collation::UTF8, "Nullable" => true, "Default" => null],
                          "AccessControlList" => ["Type" => Type::BigInt | Type::Unsigned]
                      ],
                      [
                          "Primary" => ["Fields" => ["ID", "Start", "End"]]
                      ]
                  )
                  ->Execute();
        
        //Install Module.
        /** @var \Modules\Calendar $Calendar */
        $Calendar = \vDesk\Modules::Calendar();
        $Calendar->Commands->Add(
            new Command(
                null,
                $Calendar,
                "GetEvent",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false)])
            )
        );
        $Calendar->Commands->Add(
            new Command(
                null,
                $Calendar,
                "GetEvents",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "From", \vDesk\Struct\Extension\Type::DateTime, false, false),
                    new Parameter(null, null, "To", \vDesk\Struct\Extension\Type::DateTime, false, false)
                ])
            )
        );
        $Calendar->Commands->Add(
            new Command(
                null,
                $Calendar,
                "CreateEvent",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "Start", \vDesk\Struct\Extension\Type::DateTime, false, false),
                    new Parameter(null, null, "End", \vDesk\Struct\Extension\Type::DateTime, false, false),
                    new Parameter(null, null, "FullTime", \vDesk\Struct\Type::Bool, false, false),
                    new Parameter(null, null, "RepeatAmount", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "RepeatInterval", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Title", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "Color", \vDesk\Struct\Extension\Type::Color, false, false),
                    new Parameter(null, null, "Content", \vDesk\Struct\Type::String, false, true)
                ])
            )
        );
        $Calendar->Commands->Add(
            new Command(
                null,
                $Calendar,
                "UpdateEvent",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Start", \vDesk\Struct\Extension\Type::DateTime, false, false),
                    new Parameter(null, null, "End", \vDesk\Struct\Extension\Type::DateTime, false, false),
                    new Parameter(null, null, "FullTime", \vDesk\Struct\Type::Bool, false, false),
                    new Parameter(null, null, "RepeatAmount", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "RepeatInterval", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Title", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "Color", \vDesk\Struct\Extension\Type::Color, false, false),
                    new Parameter(null, null, "Content", \vDesk\Struct\Type::String, false, true)
                ])
            )
        );
        $Calendar->Commands->Add(
            new Command(
                null,
                $Calendar,
                "UpdateEventDate",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Start", \vDesk\Struct\Extension\Type::DateTime, false, false),
                    new Parameter(null, null, "End", \vDesk\Struct\Extension\Type::DateTime, false, false)
                ])
            )
        );
        $Calendar->Commands->Add(
            new Command(
                null,
                $Calendar,
                "DeleteEvent",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false)])
            )
        );
        $Calendar->Save();
        
        //Extract files.
        static::Deploy($Phar, $Path);
        
    }
    
    /**
     * @inheritDoc
     */
    public static function Uninstall(string $Path): void {
        
        //Uninstall Module.
        /** @var \Modules\Calendar $Calendar */
        $Calendar = \vDesk\Modules::Calendar();
        $Calendar->Delete();
        
        //Delete ACLs
        foreach(
            Expression::Select("AccessControlList")
                      ->From("Calendar.Events")
            as
            $Event
        ) {
            $AccessControlList = new AccessControlList([], (int)$Event["AccessControlList"]);
            $AccessControlList->Delete();
        }
        
        //Drop database.
        Expression::Drop()
                  ->Database("Calendar")
                  ->Execute();
        
        //Delete files.
        static::Undeploy();
        
    }
}