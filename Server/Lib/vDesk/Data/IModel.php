<?php
declare(strict_types=1);

namespace vDesk\Data;

/**
 * Represents a database model.
 *
 * @package vDesk\Data
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
interface IModel extends IManagedModel {
    
    /**
     * Saves the values of the IEntity to the database.
     */
    public function Save(): void;
    
    /**
     * Deletes the IEntity from the database.
     */
    public function Delete(): void;
    
}