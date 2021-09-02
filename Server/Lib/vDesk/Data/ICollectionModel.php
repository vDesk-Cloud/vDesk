<?php
declare(strict_types=1);

namespace vDesk\Data;

use vDesk\Struct\Collections\ICollection;

/**
 * Represents a database model that contains a Collection of IModels, providing mechanisms for common CRUD operations.
 *
 * @package vDesk
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
interface ICollectionModel extends ICollection, IModel {

    /**
     * Initializes a new instance of the ICollectionModel class.
     *
     * @param iterable $Models Initializes the ICollectionModel with the specified set of Models.
     */
    public function __construct(iterable $Models = []);

}