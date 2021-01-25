<?php
declare(strict_types=1);

namespace vDesk\Data;

/**
 * Default Model for convenient handling of virtual dependency Models.
 *
 * @package vDesk\Data
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Model implements IModel {
    
    /**
     * Convenience method to create a default IModel for virtual dependency IModels.
     *
     * @return \vDesk\Data\Model A new instance of the Model class.
     */
    public function __invoke() {
        return new static();
    }
    
    /**
     * @inheritDoc
     */
    public static function FromDataView(mixed $DataView): Model {
        return new static();
    }
    
    /**
     * @inheritDoc
     */
    public function ToDataView(bool $Reference = false) {
        return null;
    }
    
    /**
     * @inheritDoc
     */
    public function ID() {
        return null;
    }
    
    /**
     * @inheritDoc
     */
    public function Fill(): Model {
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function Save(): void {
    }
    
    /**
     * @inheritDoc
     */
    public function Delete(): void {
    }
}