<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\Configuration\Settings;
use vDesk\IO\Directory;
use vDesk\IO\Path;
use vDesk\Locale\IPackage;
use vDesk\Utils\Log;

/**
 * vDesk Package manifest.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class vDesk extends Package implements IPackage {

    /**
     * The name of the Package.
     */
    public const Name = "vDesk";

    /**
     * The version of the Package.
     */
    public const Version = "1.3.2";

    /**
     * The vendor of the Package.
     */
    public const Vendor = "Kerry <DevelopmentHero@gmail.com>";

    /**
     * The description of the Package.
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
                "vDesk/Client.php",
                "vDesk/Struct",
                "vDesk/Environment",
                "vDesk/IO",
                "vDesk/Utils",
                "vDesk/Data"
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
                "View"              => "Ansicht",
                "Status"            => "Status"
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
                "View"              => "View",
                "Status"            => "Status"
            ]
        ],
        "NL" => [
            "vDesk" => [
                "About"             => "Over",
                "Activate"          => "Activeren",
                "Active"            => "Actief",
                "Administration"    => "Administratie",
                "Blue"              => "Blauw",
                "Cancel"            => "Annuleren",
                "Clear"             => "Duidelijk",
                "Close"             => "Sluiten",
                "Confirm"           => "Bevestigen",
                "SelectAll"         => "Selecteer alle",
                "Copy"              => "Kopiëren",
                "Cut"               => "Snijden",
                "Date"              => "Datum",
                "Deactivate"        => "Deactiveren",
                "Delete"            => "Verwijderen",
                "Edit"              => "Bewerken",
                "Green"             => "Groen",
                "Help"              => "Help",
                "HelpDescription"   => "Heeft u hulp nodig? Blader door de hulpcatalogus.",
                "Hue"               => "Hue",
                "Inactive"          => "Inactief",
                "Language"          => "Taal",
                "Lightness"         => "Lichtheid",
                "Logout"            => "Uitloggen",
                "LogoutDescription" => "Sluit vDesk af.",
                "Maximize"          => "Maximaliseren",
                "Minimize"          => "Minimaliseren",
                "Module"            => "Module",
                "Name"              => "Naam",
                "New"               => "Nieuw",
                "Open"              => "Open",
                "Paste"             => "Plakken",
                "Red"               => "Rood",
                "Refresh"           => "Vernieuwen",
                "Reset"             => "Resetten",
                "ResetChanges"      => "Wijzigingen resetten",
                "Restore"           => "Herstellen",
                "Saturation"        => "Saturatie",
                "Save"              => "Opslaan",
                "SaveAndClose"      => "Opslaan en afsluiten",
                "Selection"         => "Selectie",
                "Title"             => "Titel",
                "Transparency"      => "Transparantie",
                "Type"              => "Type",
                "Version"           => "Versie",
                "View"              => "Bekijk",
                "Status"            => "Status"
            ]
        ]
    ];


    /** @inheritDoc */
    public static function Install(\Phar $Phar, string $Path): void {

        //Create system structure.
        //@todo Move Client to separate Package to enable "headless" installations.
        $Client = Directory::Create($Path . Path::Separator . self::Client);
        $Design = Directory::Create($Client->Path . Path::Separator . self::Design);
        Directory::Create($Design->Path . Path::Separator . "vDesk");
        $ClientLib = Directory::Create($Client->Path . Path::Separator . self::Lib);
        Directory::Create($ClientLib->Path . Path::Separator . "vDesk");
        Directory::Create($Client->Path . Path::Separator . self::Modules);

        $Server = Directory::Create($Path . Path::Separator . self::Server);
        $Logs   = Directory::Create($Server->Path . Path::Separator . "Logs");
        //@todo Move Log to separate Package.
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
        self::Deploy($Phar, $Path);

    }

    /** @inheritDoc */
    public static function Uninstall(string $Path): void {
        //Delete files.
        Directory::Delete($Path, true);
    }

}