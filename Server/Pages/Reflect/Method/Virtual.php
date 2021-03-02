<?php
declare(strict_types=1);

namespace Pages\Reflect\Method;

use Pages\Reflect\Type;
use vDesk\Pages\Page;

/**
 * Class Virtual
 *
 * @package Pages\Reflect\Method
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Virtual extends Page {

    /**
     * Expression for parsing the signature of a '@method'-tags .
     */
    public const Signature = "/^(?(?=static )(?'Static'\S* |))(?'Type'\S*)(?'Name'[^\(]*)(?=\()\((?<=\()(?'Parameters'.*)(?=\))\)(?<=\)) (?'Description'.*)/s";

    /**
     *
     * @param null|iterable                             $Values
     * @param null|iterable                             $Templates
     * @param null|iterable                             $Stylesheets
     * @param null|iterable                             $Scripts
     * @param string                                    $Signature
     * @param bool                                      $Static
     * @param null|string                               $Modifier
     * @param null|string                               $Name
     * @param null|\Pages\Reflect\Type                  $ReturnType
     * @param string                                    $Description
     * @param \Pages\Reflect\Method\Parameter\Virtual[] $Parameters
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Reflect/Property/Virtual"],
        ?iterable $Stylesheets = ["Reflect/Stylesheet"],
        ?iterable $Scripts = [],
        string $Signature = "",
        public bool $Static = false,
        public ?string $Modifier = null,
        public ?string $Name = null,
        public ?Type $ReturnType = null,
        public string $Description = "",
        public array $Parameters = []
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
        if((bool)\preg_match(static::Signature, $Signature, $Matches)) {
            $this->Static      = $Matches["Static"] !== "";
            $this->Modifier    = \trim($Matches["Static"]) . " public";
            $this->Name        = \trim($Matches["Name"]);
            $this->ReturnType  = new Type($Matches["Type"]);
            $this->Name        = \trim($Matches["Name"]);
            $this->Description = \trim($Matches["Description"]);
            $this->Parameters  = [];
            foreach(\explode(",", $Matches["Parameters"]) as $Parameter) {
                $this->Parameters[] = new Parameter\Virtual(Signature: \trim($Parameter));
            }
        }
    }

}