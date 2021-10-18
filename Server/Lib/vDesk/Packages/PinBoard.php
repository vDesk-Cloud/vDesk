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
 * PinBoard Package manifest class.
 *
 * @package vDesk\PinBoard
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class PinBoard extends Package implements Locale\IPackage, Events\IPackage {
    
    /**
     * The name of the Package.
     */
    public const Name = "PinBoard";
    
    /**
     * The version of the Package.
     */
    public const Version = "1.0.1";
    
    /**
     * The name of the Package.
     */
    public const Vendor = "Kerry <DevelopmentHero@gmail.com>";
    
    /**
     * The name of the Package.
     */
    public const Description = "Package providing functionality for organizing notes and pinning Archive elements.";
    
    /**
     * The dependencies of the Package.
     */
    public const Dependencies = ["Archive" => "1.0.0"];
    
    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Client => [
            self::Design  => [
                "vDesk/PinBoard.css",
                "vDesk/PinBoard"
            ],
            self::Modules => [
                "PinBoard.js"
            ],
            self::Lib     => [
                "vDesk/PinBoard.js",
                "vDesk/PinBoard"
            ]
        ],
        self::Server => [
            self::Modules => [
                "PinBoard.php"
            ],
            self::Lib     => [
                "vDesk/PinBoard"
            ]
        ]
    ];
    
    /**
     * The eventlisteners of the Package.
     */
    public const Events = [
        "vDesk.Security.User.Deleted"   => "/vDesk/PinBoard/vDesk.Security.User.Deleted.php",
        "vDesk.Archive.Element.Deleted" => "/vDesk/PinBoard/vDesk.Archive.Element.Deleted.php"
    ];
    
    /**
     * The translations of the Package.
     */
    public const Locale = [
        "DE" => [
            "PinBoard" => [
                "Blue"            => "Blau",
                "Custom"          => "Benutzerdefiniert",
                "CustomColor"     => "Benutzerdefinierte Farbe",
                "Green"           => "Grün",
                "Module"          => "Pinnwand",
                "NewNote"         => "Neue Notiz",
                "Note"            => "Notiz",
                "Notes"           => "Notizen",
                "Red"             => "Rot",
                "White"           => "Weiß",
                "Yellow"          => "Gelb",
                "NoteCount"       => "Anzahl Notizen",
                "AttachmentCount" => "Anzahl Anhänge"
            ]
        ],
        "EN" => [
            "PinBoard" => [
                "Blue"            => "Blue",
                "Custom"          => "Custom",
                "CustomColor"     => "Custom color",
                "Green"           => "Green",
                "Module"          => "Pinboard",
                "NewNote"         => "New note",
                "Note"            => "Note",
                "Notes"           => "Notes",
                "Red"             => "Red",
                "White"           => "White",
                "Yellow"          => "Yellow",
                "NoteCount"       => "Amount of notes",
                "AttachmentCount" => "Amount of attachments"
            ]
        ]
    ];
    
    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        
        Expression::Create()
                  ->Schema("PinBoard")
                  ->Execute();
        
        Expression::Create()
                  ->Table(
                      "PinBoard.Notes",
                      [
                          "ID"      => ["Type" => Type::BigInt | Type::Unsigned, "Autoincrement" => true],
                          "Owner"   => ["Type" => Type::BigInt | Type::Unsigned],
                          "X"       => ["Type" => Type::SmallInt | Type::Unsigned],
                          "Y"       => ["Type" => Type::SmallInt | Type::Unsigned],
                          "Width"   => ["Type" => Type::SmallInt | Type::Unsigned],
                          "Height"  => ["Type" => Type::SmallInt | Type::Unsigned],
                          "Color"   => ["Type" => Type::VarChar, "Size" => 22, "Collation" => Collation::ASCII],
                          "Content" => ["Type" => Type::Text, "Collation" => Collation::UTF8]
                      ],
                      [
                          "Primary" => ["Fields" => ["ID", "Owner"]]
                      ]
                  )
                  ->Execute();
        Expression::Create()
                  ->Table(
                      "PinBoard.Attachments",
                      [
                          "ID"      => ["Type" => Type::BigInt | Type::Unsigned, "Autoincrement" => true],
                          "Owner"   => ["Type" => Type::BigInt | Type::Unsigned],
                          "Element" => ["Type" => Type::BigInt | Type::Unsigned],
                          "X"       => ["Type" => Type::SmallInt | Type::Unsigned],
                          "Y"       => ["Type" => Type::SmallInt | Type::Unsigned]
                      ],
                      [
                          "Primary" => ["Fields" => ["ID", "Owner"]]
                      ]
                  )
                  ->Execute();
        
        //Install Module.
        /** @var \Modules\PinBoard $PinBoard */
        $PinBoard = \vDesk\Modules::PinBoard();
        $PinBoard->Commands->Add(
            new Command(
                null,
                $PinBoard,
                "GetEntries",
                true,
                false,
            )
        );
        $PinBoard->Commands->Add(
            new Command(
                null,
                $PinBoard,
                "CreateNote",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "X", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Y", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Width", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Height", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Color", \vDesk\Struct\Extension\Type::Color, false, false),
                    new Parameter(null, null, "Content", \vDesk\Struct\Type::String, true, true)
                ])
            )
        );
        $PinBoard->Commands->Add(
            new Command(
                null,
                $PinBoard,
                "UpdateNote",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "X", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Y", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Width", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Height", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Color", \vDesk\Struct\Extension\Type::Color, false, false),
                    new Parameter(null, null, "Content", \vDesk\Struct\Type::String, false, false)
                ])
            )
        );
        //@todo Make Parameters of UpdateNote-Command optional and remove the following 3 Commands.
        $PinBoard->Commands->Add(
            new Command(
                null,
                $PinBoard,
                "UpdateNotePosition",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "X", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Y", \vDesk\Struct\Type::Int, false, false)
                ])
            )
        );
        $PinBoard->Commands->Add(
            new Command(
                null,
                $PinBoard,
                "UpdateNoteSize",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Width", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Height", \vDesk\Struct\Type::Int, false, false)
                ])
            )
        );
        $PinBoard->Commands->Add(
            new Command(
                null,
                $PinBoard,
                "UpdateNoteSize",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Width", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Height", \vDesk\Struct\Type::Int, false, false)
                ])
            )
        );
        $PinBoard->Commands->Add(
            new Command(
                null,
                $PinBoard,
                "UpdateNoteColor",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Color", \vDesk\Struct\Extension\Type::Color, false, false)
                ])
            )
        );
        $PinBoard->Commands->Add(
            new Command(
                null,
                $PinBoard,
                "UpdateNoteContent",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Content", \vDesk\Struct\Type::String, false, false)
                ])
            )
        );
        $PinBoard->Commands->Add(
            new Command(
                null,
                $PinBoard,
                "DeleteNote",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false)])
            )
        );
        $PinBoard->Commands->Add(
            new Command(
                null,
                $PinBoard,
                "CreateAttachment",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "X", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Y", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Element", \vDesk\Struct\Type::Int, false, false)
                ])
            )
        );
        $PinBoard->Commands->Add(
            new Command(
                null,
                $PinBoard,
                "UpdateAttachmentPosition",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "X", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Y", \vDesk\Struct\Type::Int, false, false)
                ])
            )
        );
        $PinBoard->Commands->Add(
            new Command(
                null,
                $PinBoard,
                "DeleteAttachment",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false)])
            )
        );
        $PinBoard->Save();
        
        //Extract files.
        self::Deploy($Phar, $Path);
        
    }
    
    /**
     * @inheritDoc
     */
    public static function Uninstall(string $Path): void {
        
        //Uninstall Module.
        /** @var \Modules\PinBoard $PinBoard */
        $PinBoard = \vDesk\Modules::PinBoard();
        $PinBoard->Delete();
        
        //Drop database.
        Expression::Drop()
                  ->Schema("PinBoard")
                  ->Execute();
        
        //Delete files.
        self::Undeploy();
        
    }
}