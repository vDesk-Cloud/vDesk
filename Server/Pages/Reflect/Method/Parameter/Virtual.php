<?php
declare(strict_types=1);

namespace Pages\Reflect\Method\Parameter;

use Pages\Reflect\Type;
use vDesk\Pages\Page;

/**
 * Method\Parameter\Virtual Page class.
 *
 * @package Reflect
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Virtual extends Page {

    /**
     * Initializes a new instance of the Method\Parameter\Virtual Page class.
     *
     * @param null|iterable            $Values      Initializes the Method\Parameter\Virtual Page with the specified Dictionary of values.
     * @param null|iterable            $Templates   Initializes the Method\Parameter\Virtual Page with the specified Collection of templates.
     * @param null|iterable            $Stylesheets Initializes the Method\Parameter\Virtual Page with the specified Collection of stylesheets.
     * @param null|iterable            $Scripts     Initializes the Method\Parameter\Virtual Page with the specified Collection of scripts.
     * @param string                   $Signature   Initializes the Method\Parameter\Virtual Page with the specified signature.
     * @param null|\Pages\Reflect\Type $Type        Initializes the Method\Parameter\Virtual Page with the specified Type.
     * @param string                   $Name        Initializes the Method\Parameter\Virtual Page with the specified name.
     * @param null|bool                $Nullable    Initializes the Method\Parameter\Virtual Page with the specified flag indicating whether the Method\Parameter\Virtual is nullable.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Reflect/Method/Parameters/Virtual"],
        ?iterable $Stylesheets = [],
        ?iterable $Scripts = [],
        string $Signature = "",
        public ?Type $Type = null,
        public string $Name = "",
        public ?bool $Nullable = null
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
        $Chunks = \explode(" ", $Signature);
        if(\count($Chunks) > 1) {
            $this->Type = new Type($Chunks[0]);
            $this->Name = $Chunks[1];
        } else {
            $this->Type = new Type(Signature: "mixed");
            $this->Name = $Chunks[0];
        }
        $this->Nullable = $this->Type->Nullable;
    }
}