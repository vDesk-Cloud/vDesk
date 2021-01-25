<?php
declare(strict_types=1);

namespace vDesk\Data;

use vDesk\Data\VersionControl\Version;
use vDesk\Struct\Properties;
use vDesk\Struct\Property\Getter;

/**
 * Trait Versioning for ...
 *
 * @package vDesk\Data
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
trait VersionControl {
    
    use Properties;
    
    /** @var null */
    private $_mContent = null;
    
    /** @var null */
    private $_oWorkingCopy = null;
    
    /**
     * Initializes a new instance of the VersionControl class.
     */
    public function __construct() {
        $this->AddProperties([
            "Content"     => [
                Get => Getter::Create()
            ],
            "WorkingCopy" => [
                Get => function(): Version {
                    if($this->_oWorkingCopy === null) {
                        //$this->_oWorkingCopy = $this->
                    }
                    return $this->_oWorkingCopy;
                }
            ]
        ]);
    }
    
    /**
     * @param \vDesk\Data\VersionControl\Version $Version
     *
     */
    public function Commit(Version $Version): void {

    }
    
    /**
     * Gets the ID of the
     *
     * @return int|null
     */
    protected abstract function GetVersionControlID(): ?int;
    
}