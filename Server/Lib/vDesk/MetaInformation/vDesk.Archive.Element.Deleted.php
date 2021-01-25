<?php

use vDesk\Archive\Element\Deleted;
use vDesk\DataProvider\Expression;
use vDesk\Events\EventListener;

/**
 * Eventlistener that listens on the 'vDesk.Archive.Element.Deleted'-Event and deletes eventual DataSets.
 */
return new EventListener(
    Deleted::Name,
    static function($Arguments) {
        //Check if the Element has metadata.
        $Result = Expression::Select("ID")
                            ->From("MetaInformation.DataSets")
                            ->Where(["Element" => $Arguments->ID])
                            ->Execute();
        if($Result->Count > 0) {
            Expression::Delete()
                      ->From("MetaInformation.DataSetRows")
                      ->Where(["DataSet" => $Result->ToValue()])
                      ->Execute();
            Expression::Delete()
                      ->From("MetaInformation.DataSets")
                      ->Where(["Element" => $Arguments->ID])
                      ->Execute();
        }
    }
);
