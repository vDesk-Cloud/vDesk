<?php
declare(strict_types=1);

namespace Modules;

use vDesk\Archive\Element;
use vDesk\DataProvider\Expression;
use vDesk\DataProvider\Expression\Functions;
use vDesk\Modules\Command;
use vDesk\MetaInformation\DataSet;
use vDesk\MetaInformation\Mask;
use vDesk\Modules\Module;
use vDesk\Search\Results;
use vDesk\Search\Result;
use vDesk\Security\AccessControlList;
use vDesk\Security\UnauthorizedAccessException;
use vDesk\Utils\Log;

/**
 * Central module for creating DataSets defining metadata of Elements and administrating Masks which define schemas for DataSets.
 *
 * @package Archive/MetaInformation
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
final class MetaInformation extends Module {
    
    /**
     * Gets all existing Masks.
     *
     * @return array An array containing the data views of all existing Masks.
     */
    public static function GetMasks(): array {
        $Masks = [];
        foreach(
            Expression::Select("ID")
                      ->From("MetaInformation.Masks")
            as
            $Mask
        ) {
            $Masks[] = (new Mask([], (int)$Mask["ID"]))->Fill()->ToDataView();
        }
        return $Masks;
    }
    
    /**
     * Creates a new Mask.
     *
     * @param string|null   $Name The name of the Mask.
     * @param object[]|null $Rows The Rows of the Mask.
     *
     * @return \vDesk\MetaInformation\Mask The newly created Mask.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to create new Masks.
     *
     */
    public static function CreateMask(string $Name = null, array $Rows = null): Mask {
        if(!\vDesk::$User->Permissions["CreateMask"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to create Mask without having permissions.");
            throw new UnauthorizedAccessException();
        }
        
        $Mask = new Mask([], null, $Name ?? Command::$Parameters["Name"]);
        
        //Append new rows.
        foreach($Rows ?? Command::$Parameters["Rows"] as $Row) {
            $Mask->Add(
                new Mask\Row(
                    null,
                    $Mask,
                    $Row["Index"],
                    $Row["Name"],
                    $Row["Type"],
                    $Row["Required"],
                    $Row["Validator"] ?? null
                )
            );
        }
        
        $Mask->Save();
        (new Mask\Created($Mask))->Dispatch();
        return $Mask;
        
    }
    
    /**
     * Updates a Mask.
     *
     * @param int|null      $ID     The ID of the Mask to update.
     * @param string|null   $Name   The new name of the Mask.
     * @param object[]|null $Add    The Rows to add.
     * @param object[]|null $Update The Rows to update.
     * @param int[]|null    $Delete The IDs of the Rows to delete.
     *
     * @return \vDesk\MetaInformation\Mask The updated Mask.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to update Masks.
     */
    public static function UpdateMask(int $ID = null, string $Name = null, array $Add = null, array $Update = null, array $Delete = null): Mask {
        if(!\vDesk::$User->Permissions["UpdateMask"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to update Mask " . Command::$Parameters["ID"] . " without having permissions.");
            throw new UnauthorizedAccessException();
        }
        
        $Mask       = (new Mask([], $ID ?? Command::$Parameters["ID"]))->Fill();
        $Mask->Name = $Name ?? Command::$Parameters["Name"];
        
        //Delete removed rows.
        foreach($Delete ?? Command::$Parameters["Delete"] as $Deleted) {
            $Mask->Remove($Mask->Find(static fn(Mask\Row $Row): bool => $Row->ID === $Deleted));
        }
        
        //Update changed rows.
        foreach($Update ?? Command::$Parameters["Update"] as $Updated) {
            $MaskRow            = $Mask->Find(static fn(Mask\Row $Row): bool => $Row->ID === $Updated["ID"]);
            $MaskRow->Index     = $Updated["Index"];
            $MaskRow->Name      = $Updated["Name"];
            $MaskRow->Type      = $Updated["Type"];
            $MaskRow->Required  = $Updated["Required"];
            $MaskRow->Validator = $Updated["Validator"] ?? null;
        }
        
        //Append added rows.
        foreach($Add ?? Command::$Parameters["Add"] as $Added) {
            $Mask->Add(
                new Mask\Row(
                    null,
                    $Mask,
                    $Added["Index"],
                    $Added["Name"],
                    $Added["Type"],
                    $Added["Required"],
                    $Added["Validator"] ?? null
                )
            );
        }
        
        $Mask->Save();
        (new Mask\Updated($Mask))->Dispatch();
        return $Mask;
        
    }
    
    /**
     * Deletes a Mask.
     *
     * @param null|int $ID The ID of the Mask to delete.
     *
     * @return bool True if the Mask has been successfully deleted.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to delete Masks.
     */
    public static function DeleteMask(int $ID = null): bool {
        if(!\vDesk::$User->Permissions["DeleteMask"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to delete Mask " . Command::$Parameters["ID"] . " without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Mask = new Mask([], $ID ?? Command::$Parameters["ID"]);
        $Mask->Delete();
        (new Mask\Deleted($Mask))->Dispatch();
        return true;
    }
    
    /**
     * Gets the DataSet of a specified Element.
     *
     * @param \vDesk\Archive\Element|null $Element The Element to get the DataSet of.
     *
     * @return string|\vDesk\MetaInformation\DataSet The DataSet of the specified Element; otherwise, null.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to read DataSets.
     */
    public static function GetDataSet(Element $Element = null): ?DataSet {
        if(!\vDesk::$User->Permissions["ReadDataSet"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to get DataSet without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Element ??= new Element(Command::$Parameters["Element"]);
        foreach(
            Expression::Select("ID", "Mask")
                      ->From("MetaInformation.DataSets")
                      ->Where(["Element" => $Element])
            as
            $DataSet
        ) {
            return (new DataSet(
                [],
                (int)$DataSet["ID"],
                $Element,
                new Mask([], (int)$DataSet["Mask"])
            ))->Fill();
        }
        return null;
    }
    
    /**
     * Creates a new DataSet.
     *
     * @param \vDesk\Archive\Element|null      $Element The Element of the DataSet.
     * @param \vDesk\MetaInformation\Mask|null $Mask    The Mask of the DataSet.
     * @param object[]|null                    $Rows    The Rows of the DataSet.
     *
     * @return \vDesk\MetaInformation\DataSet The newly create DataSet.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to create new DataSets.
     */
    public static function CreateDataSet(Element $Element = null, Mask $Mask = null, array $Rows = null): DataSet {
        if(!\vDesk::$User->Permissions["CreateDataSet"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to create DataSet without having permissions.");
            throw new UnauthorizedAccessException();
        }
        
        $DataSet          = DataSet::FromMask(($Mask ?? new Mask([], Command::$Parameters["Mask"]))->Fill());
        $DataSet->Element = $Element ?? new Element(Command::$Parameters["Element"]);
        
        //Set values.
        foreach($Rows ?? Command::$Parameters["Rows"] as $Row) {
            /** @var DataSet\Row $DataSetRow */
            $DataSetRow = $DataSet->Find(static fn(DataSet\Row $DataRow): bool => $DataRow->Row->ID === $Row["Row"]);
            if($DataSetRow === null) {
                throw new \InvalidArgumentException("Missing argument for Row '{$DataSetRow->Row->Name}'!");
            }
            $DataSetRow->Value = $Row["Value"];
        }
        
        $DataSet->Save();
        (new DataSet\Created($DataSet))->Dispatch();
        return $DataSet;
    }
    
    /**
     * Updates a DataSet.
     *
     * @param int|null      $ID   The ID of the DataSet to update.
     * @param object[]|null $Rows The new Rows of the DataSet.
     *
     * @return \vDesk\MetaInformation\DataSet The updated DataSet.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to update DataSets.
     */
    public static function UpdateDataSet(int $ID = null, array $Rows = null): DataSet {
        if(!\vDesk::$User->Permissions["UpdateDataSet"]) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to see update DataSet  without having permissions.");
            throw new UnauthorizedAccessException();
        }
        
        $DataSet = (new DataSet([], $ID ?? Command::$Parameters["ID"]))->Fill();
        
        //Set values.
        foreach($Rows ?? Command::$Parameters["Rows"] as $Row) {
            /** @var DataSet\Row $DataSetRow */
            $DataSetRow = $DataSet->Find(static fn(DataSet\Row $DataRow): bool => $DataRow->ID === $Row["ID"]);
            if($DataSetRow === null) {
                throw new \InvalidArgumentException("Missing argument for Row '{$DataSetRow->Row->Name}'!");
            }
            $DataSetRow->Value = $Row["Value"];
        }
        
        $DataSet->Save();
        (new DataSet\Updated($DataSet))->Dispatch();
        return $DataSet;
    }
    
    /**
     * Deletes a DataSet.
     *
     * @param int|null $ID The ID of the DataSet to delete.
     *
     * @return bool True if the DataSet has been successfully deleted.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to delete DataSets.
     */
    public static function DeleteDataSet(int $ID = null): bool {
        if(!\vDesk::$User->Permissions["DeleteDataSet"]) {
            Log::Info(__METHOD__, \vDesk::$User->Name . " tried to delete DataSet without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $DataSet = new DataSet([], $ID ?? Command::$Parameters["ID"]);
        $DataSet->Delete();
        (new DataSet\Deleted($DataSet))->Dispatch();
        return true;
    }
    
    /**
     * Searches the DataSets for a specified matching set of values.
     *
     * @param int|null      $ID     The ID of the Mask the Element has been tagged.
     * @param object[]|null $Values The values to match against the values of the DataSet.
     * @param bool|null     $All    Determines whether if all values must match the value of any DataSet\Row. Defaults to false if omitted.
     * @param bool|null     $Strict Determines whether every passed value must exactly match the value of it's desired DataSet\Row.
     *
     * @return \vDesk\Search\Results The found DatSets.
     */
    public static function Search(int $ID = null, array $Values = null, bool $All = null, bool $Strict = null): Results {
        $Values  ??= Command::$Parameters["Values"];
        $Strict  ??= Command::$Parameters["Strict"] ?? false;
        $Results = new Results();
        
        $Expression = Expression::Select()
                                ->Distinct(
                                    "Elements.ID",
                                    "Elements.Name",
                                    "Elements.Extension",
                                    "Elements.Type",
                                    "Elements.AccessControlList"
                                )
                                ->From("Archive.Elements")
                                ->InnerJoin("MetaInformation.DataSets")
                                ->On([
                                    "DataSets.Element" => "Elements.ID",
                                    "DataSets.Mask"    => $ID ?? Command::$Parameters["ID"]
                                ]);
        
        if($All ?? Command::$Parameters["All"] ?? false) {
            foreach($Values as $Row) {
                $Expression->InnerJoin("MetaInformation.DataSetRows", $Alias = "Row{$Row["ID"]}")
                           ->On([
                               "DataSets.ID"    => "{$Alias}.DataSet",
                               "{$Alias}.Value" => $Strict
                                   ? $Row["Value"]
                                   : ["LIKE" => "%{$Row["Value"]}%"]
                           ]);
            }
        } else {
            $Expression->InnerJoin("MetaInformation.DataSetRows")
                       ->On([
                           "DataSets.ID" => "DataSetRows.DataSet",
                           \array_map(
                               static fn($Value): array => [
                                   "DataSetRows.Row"   => $Value["ID"],
                                   "DataSetRows.Value" => $Strict
                                       ? $Value["Value"]
                                       : ["LIKE" => "%{$Value["Value"]}%"]
                               ],
                               $Values
                           )
                       ]);
            
        }
        
        foreach($Expression as $Element) {
            if((new AccessControlList([], (int)$Element["AccessControlList"]))->Read) {
                $Results->Add(
                    new Result(
                        $Element["Name"],
                        "Element",
                        [
                            "ID"        => (int)$Element["ID"],
                            "Type"      => (int)$Element["Type"],
                            "Extension" => $Element["Extension"]
                        ]
                    )
                );
            }
        }
        return $Results;
    }
    
    /**
     * Gets the status information of the MetaInformation.
     *
     * @return null|array An array containing the amount of DataSets.
     */
    public static function Status(): ?array {
        return [
            "DataSetCount" => Expression::Select(Functions::Count("*"))
                                        ->From("MetaInformation.DataSets")()
        ];
    }
    
}
