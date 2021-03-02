<?php
declare(strict_types=1);

namespace Pages\Reflect\Method\Parameter;

use Pages\Reflect\Type;
use vDesk\Pages\Page;

/**
 * Class Virtual
 *
 * @package Pages\Reflect\Method\Parameter
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Virtual extends Page {

    /**
     * @param null|iterable            $Values
     * @param null|iterable            $Templates
     * @param null|iterable            $Stylesheets
     * @param null|iterable            $Scripts
     * @param string                   $Signature
     * @param null|\Pages\Reflect\Type $Type
     * @param string                   $Name
     * @param null|bool                $Nullable
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Reflect/Property/Virtual"],
        ?iterable $Stylesheets = ["Reflect/Stylesheet"],
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
            $this->Type = new Type("mixed");
            $this->Name = $Chunks[0];
        }
        $this->Nullable = $this->Type->Nullable;
    }
}