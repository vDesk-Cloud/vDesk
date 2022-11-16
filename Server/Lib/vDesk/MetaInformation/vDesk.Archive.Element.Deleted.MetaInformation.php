<?php

use vDesk\Archive\Element\Deleted;
use vDesk\DataProvider\Expression;

/**
 * Event listener that listens on the 'vDesk.Archive.Element.Deleted'-Event and deletes eventual DataSets.
 */
return [
    Deleted::Name,
    static function(Deleted $Event) {
        //Check if the Element has metadata.
        $Result = Expression::Select("ID")
                            ->From("MetaInformation.DataSets")
                            ->Where(["Element" => $Event->Element])
                            ->Execute();
        if($Result->Count > 0) {
            Expression::Delete()
                      ->From("MetaInformation.DataSetRows")
                      ->Where(["DataSet" => $Result->ToValue()])
                      ->Execute();
            Expression::Delete()
                      ->From("MetaInformation.DataSets")
                      ->Where(["Element" => $Event->Element])
                      ->Execute();
        }
    }
];
