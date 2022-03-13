<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\Archive\Element;
use vDesk\Configuration\Settings;
use vDesk\DataProvider\Expression;
use vDesk\DataProvider\Expression\λ;
use vDesk\DataProvider\Collation;
use vDesk\DataProvider\Type;
use vDesk\IO\Directory;
use vDesk\Locale;
use vDesk\Events;
use vDesk\IO\Path;
use vDesk\Modules\Module\Command;
use vDesk\Modules\Module\Command\Parameter;
use vDesk\Security\AccessControlList;
use vDesk\Security\Group;
use vDesk\Security\User;
use vDesk\Struct\Collections\Observable\Collection;
use vDesk\Struct\Guid;
use vDesk\Utils\Log;

/**
 * Archive Package manifest class.
 *
 * @package vDesk\Archive
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Archive extends Package implements Locale\IPackage, Events\IPackage {
    
    /**
     * The name of the Package.
     */
    public const Name = "Archive";
    
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
    public const Description = "Package providing functionality for organizing files and folders.";
    
    /**
     * The dependencies of the Package.
     */
    public const Dependencies = [
        "Events"   => "1.0.1",
        "Locale"   => "1.0.2",
        "Security" => "1.0.2",
        "Search"   => "1.0.1"
    ];
    
    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Client => [
            self::Design  => [
                "vDesk/Archive.css",
                "vDesk/Archive"
            ],
            self::Modules => [
                "Archive.js"
            ],
            self::Lib     => [
                "vDesk/Archive.js",
                "vDesk/Archive"
            ]
        ],
        self::Server => [
            self::Modules => [
                "Archive.php"
            ],
            self::Lib     => [
                "vDesk/Archive"
            ]
        ]
    ];
    
    /**
     * The eventlisteners of the Package.
     */
    public const Events = [
        "vDesk.Security.User.Deleted" => "/vDesk/Archive/vDesk.Security.User.Deleted.php"
    ];
    
    /**
     * The translations of the Package.
     */
    public const Locale = [
        "DE" => [
            "Archive"     => [
                "AddFile"              => "Datei hinzufügen",
                "Attributes"           => "Eigenschaften",
                "AttributeWindowTitle" => "Eigenschaften von",
                "Clipboard"            => "Zwischenablage",
                "CreationTime"         => "Ablagezeitpunkt",
                "Details"              => "Details",
                "Element"              => "Element",
                "Entry"                => "Archivanfang",
                "File"                 => "Datei",
                "Size"                 => "Größe",
                "Folder"               => "Ordner",
                "Module"               => "Archiv",
                "NewFolder"            => "Neuer Ordner",
                "Owner"                => "Besitzer",
                "PinBoard"             => "An die Pinnwand hängen",
                "Refresh"              => "Aktualisieren",
                "Rename"               => "Umbenennen",
                "FileCount"            => "Anzahl archivierte Dateien",
                "FolderCount"          => "Anzahl Ordner",
                "DiskUsage"            => "Gesamter Speicherplatzbedarf"
            ],
            "Permissions" => [
                "ReadAttributes" => "Legt fest ob Mitglieder der Gruppe Attribute eines Archivelements sehen können",
            ],
            "Settings"    => [
                "Archive:UploadMode" => "Legt fest, ob Dateien parallel oder hintereinander in das Archiv geladen werden."
            ]
        ],
        "EN" => [
            "Archive"     => [
                "AddFile"              => "Add file",
                "Attributes"           => "Attributes",
                "AttributeWindowTitle" => "Attributes of",
                "Clipboard"            => "Clipboard",
                "CreationTime"         => "Time of creation",
                "Details"              => "Details",
                "Element"              => "Element",
                "Entry"                => "Archive entry",
                "File"                 => "File",
                "Size"                 => "Size",
                "Folder"               => "Folder",
                "Module"               => "Archive",
                "NewFolder"            => "New folder",
                "Owner"                => "Owner",
                "PinBoard"             => "Add to pinboard",
                "Refresh"              => "Refresh",
                "Rename"               => "Rename",
                "FileCount"            => "Amount of files",
                "FolderCount"          => "Amount of folders",
                "DiskUsage"            => "Amount of used disk space"
            ],
            "Permissions" => [
                "ReadAttributes" => "Determines whether members of the group are allowed to see attributes of an archive element",
            ],
            "Settings"    => [
                "Archive:UploadMode" => "Defines whether files are loaded into the archive in parallel or consecutively."
            ]
        ]
    ];
    
    /**
     * @inheritDoc
     */
    public static function PreInstall(\Phar $Phar, string $Path): void {
        $Size = \ini_get("upload_max_filesize");
        if(\str_contains($Size, "K") || \str_contains($Size, "M") || (int)$Size < 1e+9) {
            Log::Warn(self::Name, "Package suggests setting ini value of \"upload_max_filesize\" to at least 1G.");
        }
        if((int)\ini_get("post_max_size") > 0) {
            Log::Warn(self::Name, "Package suggests setting ini value of \"post_max_size\" to 0.");
        }
        if((int)\ini_get("max_input_time") > -1) {
            Log::Warn(self::Name, "Package suggests setting ini value of \"max_input_time\" to -1.");
        }
        
        //@todo Check if lib GD is installed.?
    }
    
    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        
        //Create database.
        Expression::Create()
                  ->Schema("Archive")
                  ->Execute();
        
        //Create tables.
        Expression::Create()
                  ->Table(
                      "Archive.Elements",
                      [
                          "ID"                => ["Type" => Type::BigInt | Type::Unsigned, "Autoincrement" => true],
                          "Owner"             => ["Type" => Type::BigInt | Type::Unsigned],
                          "Parent"            => ["Type" => Type::BigInt | Type::Unsigned],
                          "Name"              => ["Type" => Type::TinyText, "Collation" => Collation::UTF8],
                          "Type"              => ["Type" => Type::TinyInt | Type::Unsigned, "Size" => 2],
                          "CreationTime"      => ["Type" => Type::DateTime, "Default" => λ::CurrentTimestamp()],
                          "Guid"              => ["Type" => Type::TinyText, "Collation" => Collation::UTF8],
                          "Extension"         => ["Type"      => Type::VarChar,
                                                  "Size"      => 10,
                                                  "Collation" => Collation::ASCII,
                                                  "Nullable"  => true,
                                                  "Default"   => null],
                          "File"              => ["Type" => Type::TinyText, "Collation" => Collation::UTF8, "Nullable" => true, "Default" => null],
                          "Size"              => ["Type" => Type::BigInt | Type::Unsigned, "Default" => 0],
                          "Thumbnail"         => ["Type"      => Type::VarChar,
                                                  "Size"      => 10000,
                                                  "Collation" => Collation::ASCII,
                                                  "Nullable"  => true,
                                                  "Default"   => null],
                          "AccessControlList" => ["Type" => Type::BigInt | Type::Unsigned]
                      ],
                      [
                          "Primary"        => ["Fields" => ["ID"]],
                          "Parent"         => ["Fields" => ["Parent"]],
                          "Eventlisteners" => ["Fields" => ["Parent", "Extension" => 3, "Name" => 186]]
                      ]
                  )
                  ->Execute();
        Expression::Create()
                  ->Table(
                      "Archive.MimeTypes",
                      [
                          "ID"        => ["Type" => Type::BigInt | Type::Unsigned],
                          "Extension" => ["Type" => Type::VarChar, "Size" => 10, "Collation" => Collation::ASCII],
                          "MimeType"  => ["Type" => Type::TinyText, "Collation" => Collation::ASCII, "Nullable" => true, "Default" => null],
                          "Icon"      => ["Type" => Type::TinyText, "Collation" => Collation::UTF8]
                      ],
                      [
                          "Primary" => ["Fields" => ["ID", "Extension" => 10]]
                      ]
                  )
                  ->Execute();
        
        //Install Module.
        /** @var \Modules\Archive $Archive */
        $Archive = \vDesk\Modules::Archive();
        $Archive->Commands->Add(
            new Command(
                null,
                $Archive,
                "GetElement",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false)])
            )
        );
        $Archive->Commands->Add(
            new Command(
                null,
                $Archive,
                "GetElements",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false)])
            )
        );
        $Archive->Commands->Add(
            new Command(
                null,
                $Archive,
                "GetBranch",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false)])
            )
        );
        $Archive->Commands->Add(
            new Command(
                null,
                $Archive,
                "Upload",
                true,
                true,
                null,
                new Collection([
                    new Parameter(null, null, "Parent", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Name", \vDesk\Struct\Type::String, false, true),
                    new Parameter(null, null, "File", \vDesk\Struct\Extension\Type::File, false, true)
                ])
            )
        );
        $Archive->Commands->Add(
            new Command(
                null,
                $Archive,
                "Download",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false)])
            )
        );
        $Archive->Commands->Add(
            new Command(
                null,
                $Archive,
                "GetAttributes",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false)])
            )
        );
        $Archive->Commands->Add(
            new Command(
                null,
                $Archive,
                "CreateFolder",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "Parent", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Name", \vDesk\Struct\Type::String, false, true)
                ])
            )
        );
        $Archive->Commands->Add(
            new Command(
                null,
                $Archive,
                "Move",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "Target", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Elements", \vDesk\Struct\Type::Array, false, false)
                ])
            )
        );
        $Archive->Commands->Add(
            new Command(
                null,
                $Archive,
                "Copy",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "Target", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Elements", \vDesk\Struct\Type::Array, false, false)
                ])
            )
        );
        $Archive->Commands->Add(
            new Command(
                null,
                $Archive,
                "Rename",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Name", \vDesk\Struct\Type::String, false, false)
                ])
            )
        );
        $Archive->Commands->Add(
            new Command(
                null,
                $Archive,
                "UpdateFile",
                true,
                true,
                null,
                new Collection([
                    new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "File", \vDesk\Struct\Extension\Type::File, false, true)
                ])
            )
        );
        $Archive->Commands->Add(
            new Command(
                null,
                $Archive,
                "DeleteElements",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "Elements", \vDesk\Struct\Type::Array, false, false)])
            )
        );
        $Archive->Save();
        
        //Create permissions.
        /** @var \Modules\Security $Security */
        $Security = \vDesk\Modules::Security();
        $Security::CreatePermission("ReadAttributes", false);
        
        //Create Archive.
        $Archive = new Element(
            null,
            User::$Current,
            new Element(0),
            "Archive",
            Element::Folder,
            new \DateTime("now"),
            Guid::Create(),
            null,
            null,
            0,
            null,
            new AccessControlList([
                AccessControlList\Entry::FromUser(new User(User::System), true, true, false),
                AccessControlList\Entry::FromGroup(new Group(Group::Everyone), true, true, false)
            ])
        );
        $Archive->Save();
        
        $System = new Element(
            null,
            User::$Current,
            $Archive,
            "System",
            Element::Folder,
            new \DateTime("now"),
            Guid::Create(),
            null,
            null,
            0,
            null,
            new AccessControlList([
                AccessControlList\Entry::FromUser(new User(User::System), true, true, false),
                AccessControlList\Entry::FromGroup(new Group(Group::Everyone), false, false, false)
            ])
        );
        $System->Save();
        
        $Files                       = Directory::Create($Path . Path::Separator . self::Server . Path::Separator . "Files");
        Settings::$Local["Archive"]  = new Settings\Local\Settings(["Directory" => $Files->Path], "Archive");
        Settings::$Remote["Archive"] = new Settings\Remote\Settings(
            [
                "UploadMode" => new Settings\Remote\Setting(
                    "UploadMode",
                    "Parallel",
                    \vDesk\Struct\Extension\Type::Enum,
                    false,
                    true,
                    [
                        "Parallel",
                        "Queue"
                    ]
                )
            ],
            "Archive"
        );
        
        //Extract files.
        self::Deploy($Phar, $Path);
        
    }
    
    /**
     * @inheritDoc
     */
    public static function Uninstall(string $Path): void {
        
        //Uninstall Module.
        /** @var \Modules\Archive $Archive */
        $Archive = \vDesk\Modules::Archive();
        $Archive->Delete();
        
        //Delete ACLs
        foreach(
            Expression::Select("AccessControlList")
                      ->From("Archive.Elements")
            as
            $Element
        ) {
            $AccessControlList = new AccessControlList([], (int)$Element["AccessControlList"]);
            $AccessControlList->Delete();
        }
        
        //Drop database.
        Expression::Drop()
                  ->Schema("Archive")
                  ->Execute();
        
        //Delete files.
        Directory::Delete($Path . Path::Separator . self::Server . Path::Separator . "Files", true);
        self::Undeploy();
        
    }
}