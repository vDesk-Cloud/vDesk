<?php
declare(strict_types=1);

namespace Pages\Reflect;


use Pages\Reflect;
use vDesk\Struct;

class Type extends Reflect {

    /**
     *
     * @param null|iterable $Values
     * @param null|iterable $Templates
     * @param null|iterable $Stylesheets
     * @param null|iterable $Scripts
     * @param string        $Signature
     * @param string        $Name
     * @param bool          $Internal
     * @param bool          $Nullable
     * @param bool          $Scalar
     * @param bool          $TypedArray
     * @param array         $UnionTypes
     */
    public function __construct(

        ?iterable $Values = [],
        ?iterable $Templates = ["Reflect/Type"],
        ?iterable $Stylesheets = ["Reflect/Stylesheet"],
        ?iterable $Scripts = [],
        public string $Signature = "mixed",
        public string $Name = "mixed",
        public bool $Internal = false,
        public bool $Nullable = false,
        public bool $Scalar = false,
        public bool $TypedArray = false,
        public array $UnionTypes = []
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts);
        $this->Signature = \trim($Signature);
        if(\str_contains($this->Signature, "?")) {
            $this->Signature = \str_replace("?", "", $this->Signature);
            $this->Nullable  = true;
        }
        $Type = \strtolower($this->Signature);
        //$Type = $this->Signature;
        if(
            $Type === Struct\Type::Mixed
            || $Type === Struct\Type::Array
            || $Type === Struct\Type::Iterable
            || $Type === Struct\Type::Callable
            || $Type === Struct\Type::Null
            || $Type === Struct\Type::Resource
            || $Type === "void"
            || $Type === "self"
            || $Type === "static"
            || $Type === "integer"
            || Struct\Type::IsScalarType($Type)
        ) {
            $this->Scalar = true;
            $this->Name   = $Type;
        } else if(\str_contains($this->Signature, "|")) {
            $this->Name = $this->Signature;
            foreach(\explode("|", $this->Signature) as $UnionType) {
                $this->UnionTypes[] = new static(Signature: $UnionType);
            }
            $this->Name   = "[]";
            $this->Scalar = true;
        } else if(\str_contains($this->Signature, "[]")) {
           // $this->Name       = \str_replace("[]", "", $this->Signature);
            $this->Name       = Struct\Type::Array;
            $this->Scalar     = true;
          //  $this->Scalar     = Struct\Type::IsScalarType($this->Name);
            $this->TypedArray = true;
        } else {
            try {
                $Reflector      = new \ReflectionClass($this->Signature);
                $this->Internal = $Reflector->isInternal();
                $this->Name     = $this->Signature;
            } catch(\Throwable $e) {
                $this->Name   = "mixed";
                $this->Scalar = true;
            }

        }

    }

}
