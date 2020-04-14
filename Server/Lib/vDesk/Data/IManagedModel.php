<?php
declare(strict_types=1);

namespace vDesk\Data;

/**
 * Represents a managed read only database model.
 *
 * @package vDesk\Data
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
interface IManagedModel extends IDataView {

    /**
     * Gets the identifier of the IManagedModel.
     *
     * @return mixed The ID of the IManagedModel.
     */
    public function ID();

    /**
     * Fills the IManagedModel with its values stored in the database.
     *
     * @return \vDesk\Data\IManagedModel The filled IManagedModel.
     */
    public function Fill(): IManagedModel;

    /**
     * Creates a data view of the IManagedModel.
     *
     * @param bool $Reference Flag indicating whether the data view should represent only a reference to the IManagedModel.
     *
     * @return mixed The data view representing the current state of the IManagedModel.
     */
    public function ToDataView(bool $Reference = false);

}
