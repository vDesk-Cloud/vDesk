<?php
declare(strict_types=1);

namespace Pages\Reflect\Property;

use Pages\Reflect\Type;
use vDesk\Pages\Page;

/**
 * Class Property
 *
 * @package Pages\Reflect
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Virtual extends Page {

    /**
     * Expression for parsing the '@property'-tags of DocBlocks.
     */
    public const Signature = "/(?'Type'^\S*)\s*(?'Name'\S*)\s*(?'Description'.*)/";

    /**
     *
     * @param null|iterable            $Values
     * @param null|iterable            $Templates
     * @param null|iterable            $Stylesheets
     * @param null|iterable            $Scripts
     * @param string                   $Signature
     * @param string                   $Name
     * @param null|\Pages\Reflect\Type $Type
     * @param string                   $Description
     * @param null|bool                $Readonly
     * @param null|bool                $Writeonly
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Reflect/Property/Virtual"],
        ?iterable $Stylesheets = ["Reflect/Stylesheet"],
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
            $this->Type        = new Type(Signature:\strpos($Matches["Type"], "[]") > 0 ? "array" : $Matches["Type"]);
            $this->Description = \trim(\str_replace(" * ", "", $Matches["Description"]));
        }
    }

}