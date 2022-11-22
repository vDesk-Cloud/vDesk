<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\Collation;
use vDesk\DataProvider\Type;
use vDesk\Locale;
use vDesk\Events;
use vDesk\Modules\Module\Command;
use vDesk\Modules\Module\Command\Parameter;
use vDesk\Struct\Collections\Observable\Collection;

/**
 * Messenger Package manifest class.
 *
 * @package vDesk\Messenger
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Messenger extends Package implements Events\IPackage, Locale\IPackage {

    /**
     * The name of the Package.
     */
    public const Name = "Messenger";

    /**
     * The version of the Package.
     */
    public const Version = "1.1.1";

    /**
     * The vendor of the Package.
     */
    public const Vendor = "Kerry <DevelopmentHero@gmail.com>";

    /**
     * The description of the Package.
     */
    public const Description = "Package providing private user-, group- and room based chats.";

    /**
     * The dependencies of the Package.
     */
    public const Dependencies = [
        "Events"   => "1.2.0",
        "Locale"   => "1.0.3",
        "Security" => "1.0.4"
    ];

    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Client => [
            self::Design  => [
                "vDesk/Messenger.css",
                "vDesk/Messenger"
            ],
            self::Modules => [
                "Messenger.js"
            ],
            self::Lib     => [
                "vDesk/Messenger.js",
                "vDesk/Messenger"
            ]
        ],
        self::Server => [
            self::Modules => [
                "Messenger.php"
            ],
            self::Lib     => [
                "vDesk/Messenger"
            ]
        ]
    ];

    /**
     * The Event listeners of the Package.
     */
    public const Events = [
        "/vDesk/Archive/vDesk.Security.User.Deleted.Archive.php"
    ];

    /**
     * The translations of the Package.
     */
    public const Locale = [
        "DE" => [
            "Messenger" => [
                "Messages"          => "Nachrichten",
                "MessageCount"      => "Anzahl privater Nachrichten",
                "GroupMessageCount" => "Anzahl Gruppen-Nachrichten"
            ]
        ],
        "EN" => [
            "Messenger" => [
                "Messages"          => "Messages",
                "MessageCount"      => "Amount of private messages",
                "GroupMessageCount" => "Amount of group messages"
            ]
        ],
        "NL" => [
            "Messenger" => [
                "Messages"          => "Berichten",
                "MessageCount"      => "Aantal privéberichten",
                "GroupMessageCount" => "Aantal groepsberichten"
            ]
        ]
    ];

    /** @inheritDoc */
    public static function Install(\Phar $Phar, string $Path): void {

        Expression::Create()
                  ->Schema("Messenger")
                  ->Execute();

        //Create tables.
        Expression::Create()
                  ->Table(
                      "Messenger.Messages",
                      [
                          "ID"        => ["Type" => Type::BigInt | Type::Unsigned, "Autoincrement" => true],
                          "Sender"    => ["Type" => Type::BigInt | Type::Unsigned],
                          "Recipient" => ["Type" => Type::BigInt | Type::Unsigned],
                          "Status"    => ["Type" => Type::TinyInt | Type::Unsigned, "Size" => 2],
                          "Date"      => ["Type" => Type::DateTime],
                          "Text"      => ["Type" => Type::Text, "Collation" => Collation::UTF8],
                      ],
                      [
                          "Primary" => ["Fields" => ["ID", "Sender", "Recipient", "Status", "Date"]]
                      ]
                  )
                  ->Execute();
        Expression::Create()
                  ->Table(
                      "Messenger.GroupMessages",
                      [
                          "ID"     => ["Type" => Type::BigInt | Type::Unsigned, "Autoincrement" => true],
                          "Sender" => ["Type" => Type::BigInt | Type::Unsigned],
                          "Group"  => ["Type" => Type::BigInt | Type::Unsigned],
                          "Date"   => ["Type" => Type::DateTime],
                          "Text"   => ["Type" => Type::Text, "Collation" => Collation::UTF8],
                      ],
                      [
                          "Primary" => ["Fields" => ["ID", "Sender", "Group", "Date"]]
                      ]
                  )
                  ->Execute();

        //Install Module.
        /** @var \Modules\Messenger $Messenger */
        $Messenger = \vDesk\Modules::Messenger();
        $Messenger->Commands->Add(
            new Command(
                null,
                $Messenger,
                "GetUserMessages",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "Sender", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Date", \vDesk\Struct\Extension\Type::DateTime, false, false),
                    new Parameter(null, null, "Amount", \vDesk\Struct\Type::Int, false, false)
                ])
            )
        );
        $Messenger->Commands->Add(
            new Command(
                null,
                $Messenger,
                "GetUnreadUserMessages",
                true,
                false
            )
        );
        $Messenger->Commands->Add(
            new Command(
                null,
                $Messenger,
                "SendUserMessage",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "Recipient", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Text", \vDesk\Struct\Type::String, false, false)
                ])
            )
        );
        $Messenger->Commands->Add(
            new Command(
                null,
                $Messenger,
                "GetGroupMessages",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "Group", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Date", \vDesk\Struct\Extension\Type::DateTime, false, false),
                    new Parameter(null, null, "Amount", \vDesk\Struct\Type::Int, false, false)
                ])
            )
        );
        $Messenger->Commands->Add(
            new Command(
                null,
                $Messenger,
                "GetUnreadGroupMessages",
                true,
                false
            )
        );
        $Messenger->Commands->Add(
            new Command(
                null,
                $Messenger,
                "SendGroupMessage",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "Group", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Text", \vDesk\Struct\Type::String, false, false)
                ])
            )
        );
        $Messenger->Save();

        //Extract files.
        self::Deploy($Phar, $Path);
    }

    /** @inheritDoc */
    public static function Uninstall(string $Path): void {

        //Uninstall Module.
        /** @var \Modules\Messenger $Messenger */
        $Messenger = \vDesk\Modules::Messenger();
        $Messenger->Delete();

        //Drop database.
        Expression::Drop()
                  ->Schema("Messenger")
                  ->Execute();

        //Delete files.
        self::Undeploy();

    }
}