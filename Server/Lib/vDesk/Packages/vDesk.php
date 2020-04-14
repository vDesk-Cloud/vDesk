<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\Configuration\Settings;
use vDesk\DataProvider;
use vDesk\IO\Directory;
use vDesk\IO\Path;
use vDesk\Locale\IPackage;
use vDesk\Package;
use vDesk\Utils\Log;

/**
 * vDesk base Package class.
 *
 * @package vDesk\Packages\Packages
 * @author  Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
final class vDesk extends Package implements IPackage {
    
    /**
     * The name of the Package.
     */
    public const Name = "vDesk";
    
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
    public const Description = "vDesk base package containing the library files of the system.";
    
    /**
     * The dependencies of the Package.
     */
    public const Dependencies = [];
    
    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Client => [
            self::Design  => [
                "vDesk.css",
                "vDesk/Header.css",
                "vDesk/WorkSpace.css",
                "vDesk/Footer.css",
                "vDesk/MainMenu.css",
                "vDesk/MainMenu",
                "vDesk/Menu.css",
                "vDesk/Menu",
                "vDesk/ModuleList.css",
                "vDesk/ModuleList",
                "vDesk/TaskBar.css",
                "vDesk/TaskBar",
                "vDesk/LoginDialog.css",
                "vDesk/Controls",
                "vDesk/Media",
                "vDesk/AboutDialog.css"
            ],
            self::Lib     => [
                "vDesk.js",
                "vDesk/Header.js",
                "vDesk/WorkSpace.js",
                "vDesk/Footer.js",
                "vDesk/MainMenu.js",
                "vDesk/MainMenu",
                "vDesk/Menu.js",
                "vDesk/Menu",
                "vDesk/ModuleList.js",
                "vDesk/ModuleList",
                "vDesk/TaskBar.js",
                "vDesk/TaskBar",
                "vDesk/LoginDialog.js",
                "vDesk/ClipBoard.js",
                "vDesk/AboutDialog.js",
                
                //vDesk.Struct
                "vDesk/Struct.js",
                "vDesk/Struct",
                
                //vDesk.Utils
                "vDesk/Utils.js",
                "vDesk/Utils",
                
                //vDesk.Visual
                "vDesk/Visual.js",
                "vDesk/Visual",
                
                //vDesk.Media
                "vDesk/Media.js",
                "vDesk/Media",
                
                //vDesk.Controls
                "vDesk/Controls.js",
                "vDesk/Controls",
                
                //vDesk.Connection
                "vDesk/Connection.js"
            ],
            self::Modules => [
                "Modules.js"
            ]
        ],
        self::Server => [
            "vDesk.php",
            self::Lib => [
                "vDesk.php",
                "vDesk/DataProvider.php",
                "vDesk/DataProvider",
                "vDesk/Client.php",
                "vDesk/Struct",
                "vDesk/Environment",
                "vDesk/IO",
                "vDesk/Utils",
                "vDesk/Data",
                "vDesk/Connection"
            ]
        ]
    ];
    
    /**
     * The translations of the Package.
     */
    public const Locale = [
        "DE" => [
            "vDesk" => [
                "About"             => "Über",
                "Activate"          => "Aktivieren",
                "Active"            => "Aktiv",
                "Administration"    => "Administration",
                "Blue"              => "Blau",
                "Cancel"            => "Abbrechen",
                "Clear"             => "Leeren",
                "Close"             => "Schließen",
                "Confirm"           => "Übernehmen",
                "Control"           => "Steuerelement",
                "SelectAll"         => "Alles auswählen",
                "Copy"              => "Kopieren",
                "Cut"               => "Ausschneiden",
                "Date"              => "Datum",
                "Deactivate"        => "Deaktivieren",
                "Delete"            => "Löschen",
                "Edit"              => "Bearbeiten",
                "Green"             => "Grün",
                "Help"              => "Hilfe",
                "HelpDescription"   => "Benötigen Sie Hilfe? Stöbern Sie durch den Hilfekatalog.",
                "Hue"               => "Farbton",
                "Inactive"          => "Inaktiv",
                "Language"          => "Sprache",
                "Lightness"         => "Helligkeit",
                "Logout"            => "Abmelden",
                "LogoutDescription" => "vDesk beenden.",
                "Maximize"          => "Maximieren",
                "Minimize"          => "Minimieren",
                "Module"            => "Modul",
                "Name"              => "Name",
                "New"               => "Neu",
                "Open"              => "Öffnen",
                "Paste"             => "Einfügen",
                "Red"               => "Rot",
                "Refresh"           => "Aktualisieren",
                "Reset"             => "Zurücksetzen",
                "ResetChanges"      => "Änderungen verwerfen",
                "Restore"           => "Wiederherstellen",
                "Saturation"        => "Sättigung",
                "Save"              => "Speichern",
                "SaveAndClose"      => "Speichern & Schließen",
                "Selection"         => "Auswahl",
                "Title"             => "Titel",
                "Transparency"      => "Transparenz",
                "Type"              => "Typ",
                "Version"           => "Version",
                "View"              => "Ansicht"
            ]
        ],
        "EN" => [
            "vDesk" => [
                "About"             => "About",
                "Activate"          => "Activate",
                "Active"            => "Active",
                "Administration"    => "Administration",
                "Blue"              => "Blue",
                "Cancel"            => "Cancel",
                "Clear"             => "Clear",
                "Close"             => "Close",
                "Confirm"           => "Confirm",
                "SelectAll"         => "Select all",
                "Copy"              => "Copy",
                "Cut"               => "Cut",
                "Date"              => "Date",
                "Deactivate"        => "Deactivate",
                "Delete"            => "Delete",
                "Edit"              => "Edit",
                "Green"             => "Green",
                "Help"              => "Help",
                "HelpDescription"   => "Do you require help? Browse through the help catalogue.",
                "Hue"               => "Hue",
                "Inactive"          => "Inactive",
                "Language"          => "Language",
                "Lightness"         => "Lightness",
                "Logout"            => "Logout",
                "LogoutDescription" => "Exit vDesk.",
                "Maximize"          => "Maximize",
                "Minimize"          => "Minimize",
                "Module"            => "Module",
                "Name"              => "Name",
                "New"               => "New",
                "Open"              => "Open",
                "Paste"             => "Paste",
                "Red"               => "Red",
                "Refresh"           => "Refresh",
                "Reset"             => "Reset",
                "ResetChanges"      => "Reset changes",
                "Restore"           => "Restore",
                "Saturation"        => "Saturation",
                "Save"              => "Save",
                "SaveAndClose"      => "Save & close",
                "Selection"         => "Selection",
                "Title"             => "Title",
                "Transparency"      => "Transparency",
                "Type"              => "Type",
                "Version"           => "Version",
                "View"              => "View"
            ]
        ]
    ];
    
    /**
     * @inheritDoc
     */
    public static function PreInstall(\Phar $Phar, string $Path): void {
        $Provider = \readline("SQL provider [available = mysql] [default = mysql]: ");
        switch(\strtolower($Provider)) {
            case "":
            case "mysql":
                $Provider = "MySQL";
                break;
            default:
                throw new \InvalidArgumentException("Provider '{$Provider}' is not supported. Current supported Drivers: MySQL");
        }
        
        $Server   = \readline("SQL server [default = p:localhost]: ");
        $Port     = \readline("Port [default = 3306]: ");
        $User     = \readline("SQL user: ");
        $Password = \readline("SQL user password: ");
        
        Settings::$Local["DataProvider"] = new Settings\Local\Settings(
            [
                "Provider" => $Provider,
                "Server"   => $Server !== "" ? $Server : "p:localhost",
                "Port"     => $Port !== "" ? (int)$Port : 3306,
                "User"     => $User,
                "Password" => $Password
            ],
            "DataProvider"
        );
        
    }
    
    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        
        //Setup DataProvider.
        new DataProvider(
            Settings::$Local["DataProvider"]["Provider"],
            Settings::$Local["DataProvider"]["Server"],
            Settings::$Local["DataProvider"]["Port"],
            Settings::$Local["DataProvider"]["User"],
            Settings::$Local["DataProvider"]["Password"]
        );
        
        //Create system structure.
        $Client = Directory::Create($Path . Path::Separator . self::Client);
        $Design = Directory::Create($Client->Path . Path::Separator . self::Design);
        Directory::Create($Design->Path . Path::Separator . "vDesk");
        $ClientLib = Directory::Create($Client->Path . Path::Separator . self::Lib);
        Directory::Create($ClientLib->Path . Path::Separator . "vDesk");
        Directory::Create($Client->Path . Path::Separator . self::Modules);
        
        $Server                 = Directory::Create($Path . Path::Separator . self::Server);
        $Logs                   = Directory::Create($Server->Path . Path::Separator . "Logs");
        Settings::$Local["Log"] = new Settings\Local\Settings(
            [
                "Target" => $Logs->Path . Path::Separator . "Log.txt",
                "Level"  => Log::Debug,
                "Limit"  => 1024 * 1024
            ],
            "Log"
        );
        
        $ServerLib = Directory::Create($Server->Path . Path::Separator . self::Lib);
        Directory::Create($ServerLib->Path . Path::Separator . "vDesk");
        Directory::Create($Server->Path . Path::Separator . self::Modules);
        
        //Extract files.
        static::Deploy($Phar, $Path);
        
    }
    
    
    /**
     * @inheritDoc
     */
    public static function Uninstall(string $Path): void {
        
        //Delete files.
        Directory::Delete($Path, true);
        
    }
    
}