<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\Locale\IPackage;
use vDesk\Modules\Module\Command;
use vDesk\Modules\Module\Command\Parameter;
use vDesk\Struct\Collections\Observable\Collection;
use vDesk\Struct\Type;

/**
 * Search Package manifest.
 *
 * @package vDesk\Search
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Search extends Package implements IPackage {

    /**
     * The name of the Package.
     */
    public const Name = "Search";

    /**
     * The version of the Package.
     */
    public const Version = "1.0.1";

    /**
     * The vendor of the Package.
     */
    public const Vendor = "Kerry <DevelopmentHero@gmail.com>";

    /**
     * The description of the Package.
     */
    public const Description = "Package providing functionality for searching the modules of the system.";

    /**
     * The dependencies of the Package.
     */
    public const Dependencies = [
        "Modules" => "1.0.1",
        "Locale"  => "1.0.2"
    ];

    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Client => [
            self::Design  => [
                "vDesk/Search.css",
                "vDesk/Search"
            ],
            self::Modules => [
                "Search.js"
            ],
            self::Lib     => [
                "vDesk/Search.js",
                "vDesk/Search"
            ]
        ],
        self::Server => [
            self::Modules => [
                "Search.php"
            ],
            self::Lib     => [
                "vDesk/Search"
            ]
        ]
    ];

    /**
     * The translations of the Package.
     */
    public const Locale = [
        "DE" => [
            "Search" => [
                "Filters"                 => "Suchfilter",
                "MissingAction"           => "Mit diesem Suchergebnis wurde keine Aktion verknüpft",
                "MissingViewerPlugin"     => "Kein Anzeigeplugin verfügbar",
                "Module"                  => "Suche",
                "Preview"                 => "Vorschau",
                "Results"                 => "Suchergebnisse",
                "Search"                  => "Suchen",
                "SearchField"             => "Suchbegriff/e eingeben.",
                "StrictAccordance"        => "Strikte Übereinstimmung",
                "StrictAccordanceTooltip" => "Wenn aktiv, werden Datensätze nur berücksichtigt, deren Daten mit allen Suchwerten übereinstimmen.",
                "StrictComparison"        => "Strikter Wertvergleich",
                "StrictComparisonTooltip" => "Wenn aktiv, werden nur Datensätze berücksichtigt, deren Daten exakt mit den Suchwerten übereinstimmen."
            ]
        ],
        "EN" => [
            "Search" => [
                "Filters"                 => "Searchfilters",
                "MissingAction"           => "No action associated with this search result",
                "MissingViewerPlugin"     => "No preview plugin available",
                "Module"                  => "Search",
                "Preview"                 => "Preview",
                "Results"                 => "Searchresults",
                "Search"                  => "Search",
                "SearchField"             => "Enter search value/s.",
                "StrictAccordance"        => "Strict accordance",
                "StrictAccordanceTooltip" => "If set, considers only datasets which data matches all specified search values.",
                "StrictComparison"        => "Strict value comparison",
                "StrictComparisonTooltip" => "If set. considers only datasets which data exactly compare to the specified search values."
            ]
        ],
        "NL" => [
            "Search" => [
                "Filters"                 => "Zoekfilters",
                "MissingAction"           => "Geen actie geassocieerd met dit zoekresultaat",
                "MissingViewerPlugin"     => "Geen preview plugin beschikbaar",
                "Module"                  => "Zoek",
                "Preview"                 => "Voorbeeld",
                "Results"                 => "Zoekresultaten",
                "Search"                  => "Zoek",
                "SearchField"             => "Voer zoekwaarde(n) in.",
                "StrictAccordance"        => "Strikte overeenstemming",
                "StrictAccordanceTooltip" => "Indien ingesteld, worden alleen datasets in aanmerking genomen waarvan de gegevens overeenstemmen met alle opgegeven zoekwaarden.",
                "StrictComparison"        => "Strikte waardevergelijking",
                "StrictComparisonTooltip" => "Indien ingesteld, worden alleen datasets in aanmerking genomen waarvan de gegevens precies overeenkomen met de opgegeven zoekwaarden."
            ]
        ]
    ];

    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {

        //Install Module.
        /** @var \Modules\Search $Search */
        $Search = \vDesk\Modules::Search();
        $Search->Commands->Add(
            new Command(
                null,
                $Search,
                "Search",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "Value", Type::String, false, false),
                    new Parameter(null, null, "Filters", Type::Array, false, false)
                ])
            )
        );
        $Search->Save();

        //Extract files.
        self::Deploy($Phar, $Path);

    }

    /**
     * @inheritDoc
     */
    public static function Uninstall(string $Path): void {

        //Uninstall Module.
        /** @var \Modules\Search $Search */
        $Search = \vDesk\Modules::Search();
        $Search->Delete();

        //Delete files.
        self::Undeploy();

    }
}