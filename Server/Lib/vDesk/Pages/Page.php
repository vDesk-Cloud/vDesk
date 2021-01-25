<?php
declare(strict_types=1);

namespace vDesk\Pages;

use vDesk\Data\IDataView;
use vDesk\Struct\Collections\Collection;
use vDesk\Struct\Collections\Dictionary;

/**
 * Baseclass for Pages.
 *
 * @author  Kerry Holz <k.holz@artforge.eu>.
 */
class Page implements IDataView {
    
    /**
     * The templates of the Page.
     *
     * @var null|\vDesk\Struct\Collections\Collection
     */
    public ?Collection $Templates = null;
    
    /**
     * The values of the Page.
     *
     * @var null|\vDesk\Struct\Collections\Dictionary
     */
    public ?Dictionary $Values = null;
    
    /**
     * The stylesheets of the Page.
     *
     * @var null|\vDesk\Struct\Collections\Collection
     */
    public ?Collection $Stylesheets = null;
    
    /**
     * The scripts of the Page.
     *
     * @var null|\vDesk\Struct\Collections\Collection
     */
    public ?Collection $Scripts = null;
    
    /**
     * Initializes a new instance of the Page class.
     *
     * @param null|iterable $Values      Initializes the Page with the specified Dictionary of values.
     * @param null|iterable $Templates   Initializes the Page with the specified Collection of templates.
     * @param null|iterable $Stylesheets Initializes the Page with the specified Collection of stylesheets.
     * @param null|iterable $Scripts     Initializes the Page with the specified Collection of scripts.
     */
    public function __construct(?iterable $Values = [], ?iterable $Templates = [], ?iterable $Stylesheets = [], ?iterable $Scripts = []) {
        $this->Values      = new Dictionary($Values);
        $this->Templates   = new Collection($Templates);
        $this->Stylesheets = new Collection($Stylesheets);
        $this->Scripts     = new Collection($Scripts);
    }
    
    /**
     * @inheritDoc
     */
    public function ToDataView(): string {
        
        //if(static::Cached
        
        return $this->Templates->Reduce(
            function(string $Markup, $Template): string {
                if($Template instanceof IDataView) {
                    return $Markup . $Template->ToDataView();
                }
                $Values         = $this->Values->ToArray();
                $Values["Page"] = $this;
                return $Markup . Functions::Template($Template, $Values);
            },
            ""
        );
    }
    
    /**
     * @ignore
     */
    final public function __toString(): string {
        return (string)$this->ToDataView();
    }
    
    /**
     * @inheritDoc
     */
    public static function FromDataView(mixed $DataView): IDataView {
        return new static();
    }
}