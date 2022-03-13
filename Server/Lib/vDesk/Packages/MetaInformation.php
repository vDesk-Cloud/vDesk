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
 * MetaInformation Package manifest class.
 *
 * @package vDesk\MetaInformation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class MetaInformation extends Package implements Locale\IPackage, Events\IPackage {
    
    /**
     * The name of the Package.
     */
    public const Name = "MetaInformation";
    
    /**
     * The version of the Package.
     */
    public const Version = "1.0.3";
    
    /**
     * The name of the Package.
     */
    public const Vendor = "Kerry <DevelopmentHero@gmail.com>";
    
    /**
     * The name of the Package.
     */
    public const Description = "Package providing functionality for indexing and searching for files and folders of an Archive";
    
    /**
     * The dependencies of the Package.
     */
    public const Dependencies = ["Archive" => "1.0.1"];
    
    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Client => [
            self::Design => [
                "vDesk/MetaInformation"
            ],
            self::Lib    => [
                "vDesk/MetaInformation.js",
                "vDesk/MetaInformation"
            ]
        ],
        self::Server => [
            self::Modules => [
                "MetaInformation.php"
            ],
            self::Lib     => [
                "vDesk/MetaInformation"
            ]
        ]
    ];
    
    /**
     * The eventlisteners of the Package.
     */
    public const Events = [
        "vDesk.Archive.Element.Deleted" => "/vDesk/MetaInformation/vDesk.Archive.Element.Deleted.php"
    ];
    
    /**
     * The translations of the Package.
     */
    public const Locale = [
        "DE" => [
            "MetaInformation" => [
                "AddRow"        => "Neue Zeile hinzufügen",
                "Boolean"       => "Boolscher Wert",
                "ChangeMask"    => "Beim wechsel der Maske gehen vorhandene Daten verloren. Maske wechseln?",
                "Currency"      => "Währung",
                "Date"          => "Datum",
                "DateTime"      => "Datum und Zeit",
                "Decimal"       => "dezimal",
                "DeleteDataSet" => "Datensatz löschen?",
                "DeleteMask"    => "Beim Löschen der Maske gehen sämtliche unter dieser Maske erstellten Datensätze verloren. Maske löschen?",
                "DeleteRow"     => "Zeile löschen",
                "MaskDesigner"  => "Maskendesigner",
                "Max"           => "Max",
                "MetaData"      => "Metadaten",
                "Min"           => "Min",
                "Money"         => "Geldbetrag",
                "NewMask"       => "Neue Maske",
                "NoDataSet"     => "Das Element besitzt keine Metadaten.",
                "Numeric"       => "Numerisch",
                "Pattern"       => "Muster",
                "Required"      => "Pflichtfeld",
                "Steps"         => "Schritte",
                "Text"          => "Text",
                "Time"          => "Zeit",
                "DataSetCount"  => "Anzahl Datensätze"
            ],
            "Permissions"     => [
                "ReadDataSet"   => "Legt fest ob Mitglieder der Gruppe Metadatensätze sehen können",
                "CreateDataSet" => "Legt fest ob Mitglieder der Gruppe neue Metadatensätze erstellen können",
                "UpdateDataSet" => "Legt fest ob Mitglieder der Gruppe Metadatensätze bearbeiten können",
                "DeleteDataSet" => "Legt fest ob Mitglieder der Gruppe Metadatensätze löschen können",
                "CreateMask"    => "Legt fest ob Mitglieder der Gruppe neue Metadatenmasken erstellen können",
                "UpdateMask"    => "Legt fest ob Mitglieder der Gruppe Metadatenmasken bearbeiten können",
                "DeleteMask"    => "Legt fest ob Mitglieder der Gruppe Metadatenmasken löschen können"
            ]
        ],
        "EN" => [
            "MetaInformation" => [
                "AddRow"        => "Add new row",
                "Boolean"       => "Boolean value",
                "ChangeMask"    => "When changing the mask, existing data will be lost. Change mask?",
                "Currency"      => "Currency",
                "Date"          => "Date",
                "DateTime"      => "Date and time",
                "Decimal"       => "decimal",
                "DeleteDataSet" => "Delete dataset?",
                "DeleteMask"    => "Deleting the mask will cause all datasets created under this mask to be lost. Delete mask?",
                "DeleteRow"     => "Delete row",
                "MaskDesigner"  => "Maskdesigner",
                "Max"           => "Max",
                "MetaData"      => "Metadata",
                "Min"           => "Min",
                "Money"         => "Money",
                "NewMask"       => "New mask",
                "NoDataSet"     => "The Element has no metadata.",
                "Numeric"       => "Numeric",
                "Pattern"       => "Pattern",
                "Required"      => "Required",
                "Steps"         => "Steps",
                "Text"          => "Text",
                "Time"          => "Time",
                "DataSetCount"  => "Amount of datasets"
            ],
            "Permissions"     => [
                "ReadDataSet"   => "Determines whether members of the group are allowed to see metadatasets",
                "CreateDataSet" => "Determines whether members of the group are allowed to create new metadatasets",
                "UpdateDataSet" => "Determines whether members of the group are allowed to update metadatasets",
                "DeleteDataSet" => "Determines whether members of the group are allowed to delete metadatasets",
                "CreateMask"    => "Determines whether members of the group are allowed to create new metadata masks",
                "UpdateMask"    => "Determines whether members of the group are allowed to update metadata masks",
                "DeleteMask"    => "Determines whether members of the group are allowed to delete metadata masks"
            ]
        ]
    ];
    
    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        
        Expression::Create()
                  ->Schema("MetaInformation")
                  ->Execute();
        
        //Create tables.
        Expression::Create()
                  ->Table(
                      "MetaInformation.Masks",
                      [
                          "ID"   => ["Type" => Type::BigInt | Type::Unsigned, "Autoincrement" => true],
                          "Name" => ["Type" => Type::TinyText, "Collation" => Collation::UTF8]
                      ],
                      [
                          "Primary" => ["Fields" => ["ID", "Name" => 255]]
                      ]
                  )
                  ->Execute();
        Expression::Create()
                  ->Table(
                      "MetaInformation.MaskRows",
                      [
                          "ID"        => ["Type" => Type::BigInt | Type::Unsigned, "Autoincrement" => true],
                          "Mask"      => ["Type" => Type::BigInt | Type::Unsigned],
                          "Index"     => ["Type" => Type::TinyInt | Type::Unsigned],
                          "Name"      => ["Type" => Type::TinyText, "Collation" => Collation::UTF8],
                          "Type"      => ["Type" => Type::TinyText, "Collation" => Collation::ASCII],
                          "Required"  => ["Type" => Type::Boolean],
                          "Validator" => ["Type" => Type::Text, "Collation" => Collation::UTF8, "Nullable" => true, "Default" => null]
                      ],
                      [
                          "Primary" => ["Fields" => ["ID"]],
                          "Mask"    => ["Unique" => true, "Fields" => ["Mask", "Name" => 255]]
                      ]
                  )
                  ->Execute();
        Expression::Create()
                  ->Table(
                      "MetaInformation.DataSets",
                      [
                          "ID"      => ["Type" => Type::BigInt | Type::Unsigned, "Autoincrement" => true],
                          "Element" => ["Type" => Type::BigInt | Type::Unsigned],
                          "Mask"    => ["Type" => Type::BigInt | Type::Unsigned]
                      ],
                      [
                          "Primary" => ["Fields" => ["ID", "Element", "Mask"]]
                      ]
                  )
                  ->Execute();
        Expression::Create()
                  ->Table(
                      "MetaInformation.DataSetRows",
                      [
                          "ID"      => ["Type" => Type::BigInt | Type::Unsigned, "Autoincrement" => true],
                          "DataSet" => ["Type" => Type::BigInt | Type::Unsigned],
                          "Row"     => ["Type" => Type::BigInt | Type::Unsigned],
                          "Value"   => ["Type" => Type::Text, "Collation" => Collation::UTF8, "Nullable" => true, "Default" => null]
                      ],
                      [
                          "Primary" => ["Fields" => ["ID", "DataSet", "Row"]]
                      ]
                  )
                  ->Execute();
        
        //Install Module.
        /** @var \Modules\MetaInformation $MetaInformation */
        $MetaInformation = \vDesk\Modules::MetaInformation();
        $MetaInformation->Commands->Add(
            new Command(
                null,
                $MetaInformation,
                "GetMasks",
                true,
                false,
            )
        );
        $MetaInformation->Commands->Add(
            new Command(
                null,
                $MetaInformation,
                "CreateMask",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "Name", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "Rows", \vDesk\Struct\Type::Array, false, false)
                ])
            )
        );
        $MetaInformation->Commands->Add(
            new Command(
                null,
                $MetaInformation,
                "UpdateMask",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Name", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "Add", \vDesk\Struct\Type::Array, false, false),
                    new Parameter(null, null, "Update", \vDesk\Struct\Type::Array, false, false),
                    new Parameter(null, null, "Delete", \vDesk\Struct\Type::Array, false, false)
                ])
            )
        );
        $MetaInformation->Commands->Add(
            new Command(
                null,
                $MetaInformation,
                "DeleteMask",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false)])
            )
        );
        $MetaInformation->Commands->Add(
            new Command(
                null,
                $MetaInformation,
                "GetDataSet",
                true,
                false,
                null,
                new Collection([new Parameter(null, null, "Element", \vDesk\Struct\Type::Int, false, false)])
            )
        );
        $MetaInformation->Commands->Add(
            new Command(
                null,
                $MetaInformation,
                "CreateDataSet",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "Element", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Mask", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Rows", \vDesk\Struct\Type::Array, false, false)
                ])
            )
        );
        $MetaInformation->Commands->Add(
            new Command(
                null,
                $MetaInformation,
                "UpdateDataSet",
                true,
                false,
                null,
                new Collection([
                    //@todo Use the Element as Primary identifier.
                    new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Rows", \vDesk\Struct\Type::Array, false, false)
                ])
            )
        );
        $MetaInformation->Commands->Add(
            new Command(
                null,
                $MetaInformation,
                "DeleteDataSet",
                true,
                false,
                null,
                //@todo Use the Element as Primary identifier.
                new Collection([new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false)])
            )
        );
        $MetaInformation->Commands->Add(
            new Command(
                null,
                $MetaInformation,
                "Search",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Values", \vDesk\Struct\Type::Array, false, false),
                    new Parameter(null, null, "All", \vDesk\Struct\Type::Bool, false, false),
                    new Parameter(null, null, "Strict", \vDesk\Struct\Type::Bool, false, false)
                ])
            )
        );
        $MetaInformation->Save();
        
        //Create permissions.
        /** @var \Modules\Security $Security */
        $Security = \vDesk\Modules::Security();
        $Security::CreatePermission("CreateMask", false);
        $Security::CreatePermission("UpdateMask", false);
        $Security::CreatePermission("DeleteMask", false);
        $Security::CreatePermission("ReadDataSet", true);
        $Security::CreatePermission("CreateDataSet", true);
        $Security::CreatePermission("UpdateDataSet", true);
        $Security::CreatePermission("DeleteDataSet", true);
        
        //Extract files.
        self::Deploy($Phar, $Path);
    }
    
    /**
     * @inheritDoc
     */
    public static function Uninstall(string $Path): void {
        
        //Uninstall Module.
        /** @var \Modules\MetaInformation $MetaInformation */
        $MetaInformation = \vDesk\Modules::MetaInformation();
        $MetaInformation->Delete();
        
        //Delete permissions.
        /** @var \Modules\Security $Security */
        $Security = \vDesk\Modules::Security();
        $Security::DeletePermission("CreateMask");
        $Security::DeletePermission("UpdateMask");
        $Security::DeletePermission("DeleteMask");
        $Security::DeletePermission("ReadDataSet");
        $Security::DeletePermission("CreateDataSet");
        $Security::DeletePermission("UpdateDataSet");
        $Security::DeletePermission("DeleteDataSet");
        
        //Drop database.
        Expression::Drop()
                  ->Schema("MetaInformation")
                  ->Execute();
        
        //Delete files.
        self::Undeploy();
        
    }
}