<?php
declare(strict_types=1);

namespace Pages\Reflect\Method;

use Pages\Reflect\Type;
use vDesk\Pages\Page;

/**
 * Method\Virtual Page class.
 *
 * @package Reflect
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Virtual extends Page {

    /**
     * Expression for parsing the signature of a '@method'-tags .
     */
    public const Signature = "/^(?(?=static )(?'Static'\S* |))(?'Type'\S*)(?'Name'[^\(]*)(?=\()\((?<=\()(?'Parameters'.*)(?=\))\)(?<=\)) (?'Description'.*)/s";

    /**
     * Initializes a new instance of the Method\Virtual Page class.
     *
     * @param null|iterable                             $Values      Initializes the Method\Virtual Page with the specified Dictionary of values.
     * @param null|iterable                             $Templates   Initializes the Method\Virtual Page with the specified Collection of templates.
     * @param null|iterable                             $Stylesheets Initializes the Method\Virtual Page with the specified Collection of stylesheets.
     * @param null|iterable                             $Scripts     Initializes the Method\Virtual Page with the specified Collection of scripts.
     * @param string                                    $Signature   Initializes the Method\Virtual Page with the specified signature.
     * @param bool                                      $Static      Initializes the Method\Virtual Page with the specified flag indicating whether the Method\Virtual is nullable.
     * @param null|string                               $Modifier    Initializes the Method\Virtual Page with the specified modifier.
     * @param null|string                               $Name        Initializes the Member Page with the specified name.
     * @param null|\Pages\Reflect\Type                  $ReturnType  Initializes the Member Page with the specified return Type.
     * @param string                                    $Description Initializes the Member Page with the specified description.
     * @param \Pages\Reflect\Method\Parameter\Virtual[] $Parameters  Initializes the Method\Virtual Page with the specified set of Parameters.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Reflect/Method/Virtual"],
        ?iterable $Stylesheets = [],
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