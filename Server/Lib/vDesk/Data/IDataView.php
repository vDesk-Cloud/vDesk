<?php
declare(strict_types=1);

namespace vDesk\Data;

/**
 * Provides mechanisms for displaying data of objects.
 *
 * @package vDesk\Data
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
interface IDataView {
    
    /**
     * Creates an IDataView from a specified data view.
     *
     * @param mixed $DataView The data to use to create an IDataView.
     *
     * @return \vDesk\Data\IDataView An IDataView created from the specified data view.
     */
    public static function FromDataView(mixed $DataView): IDataView;
    
    /**
     * Creates a data view of the IDataView.
     *
     * @return mixed The data view representing the current state of the IDataView.
     */
    public function ToDataView();
}
