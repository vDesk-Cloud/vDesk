<?php
declare(strict_types=1);

namespace Pages\Reflect\Property;

use Pages\Reflect\Type;
use vDesk\Pages\Page;

/**
 * Property\Virtual Page class.
 *
 * @package Reflect
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Virtual extends Page {

    /**
     * Expression for parsing the '@property'-tags of DocBlocks.
     */
    public const Signature = "/(?'Type'^\S*)\s*(?'Name'\S*)\s*(?'Description'.*)/";

    /**
     * Initializes a new instance of the Property\Virtual Page class.
     *
     * @param null|iterable            $Values      Initializes the Property\Virtual Page with the specified Dictionary of values.
     * @param null|iterable            $Templates   Initializes the Property\Virtual Page with the specified Collection of templates.
     * @param null|iterable            $Stylesheets Initializes the Property\Virtual Page with the specified Collection of stylesheets.
     * @param null|iterable            $Scripts     Initializes the Property\Virtual Page with the specified Collection of scripts.
     * @param string                   $Signature   Initializes the Property\Virtual Page with the specified signature.
     * @param string                   $Name        Initializes the Property\Virtual Page with the specified name.
     * @param null|\Pages\Reflect\Type $Type        Initializes the Property\Virtual Page with the specified Type.
     * @param string                   $Description Initializes the Property\Virtual Page with the specified description.
     * @param null|bool                $Readonly    Initializes the Property\Virtual Page with the specified flag indicating whether the Property\Virtual is read only.
     * @param null|bool                $Writeonly   Initializes the Property\Virtual Page with the specified flag indicating whether the Property\Virtual is write only.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Reflect/Property/Virtual"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
        string $Signature = "",
        public string $Name = "",
        public ?Type $Type = null,
        public string $Description = "",
        public ?bool $Readonly = null,
        public ?bool $Writeonly = null
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
        $Matches = [];
        if((bool)\preg_match(static::Signature, $Signature, $Matches)) {
            $this->Name        = \str_replace("$", "", \trim($Matches["Name"]));
            $this->Type        = new Type(Signature: \strpos($Matches["Type"], "[]") > 0 ? "array" : $Matches["Type"]);
            $this->Description = \trim(\str_replace(" * ", "", $Matches["Description"]));
        }
    }

}