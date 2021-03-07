<?php
declare(strict_types=1);

namespace Pages\Reflect;

use Pages\Reflect;
use Pages\Reflect\Method\Parameter;

/**
 * Method Page class.
 *
 * @package Reflect
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Method extends Reflect {

    /**
     * Enumeration of magic methods.
     */
    public const Magic = [
        "__get",
        "__set",
        "__call",
        "__callStatic",
        "__construct",
        "__destruct",
        "__isset",
        "__unset",
        "__sleep",
        "__wakeup",
        "__toString",
        "__invoke",
        "__set_state",
        "__clone",
        "__debugInfo"
    ];

    /**
     * Expression for parsing the '@param'-tags of DocBlocks.
     */
    public const Parameter = "/(?<= \* @param ).*?(?= \* @|\*\/)/s";

    /**
     * Expression for parsing the signature of a '@param' string.
     */
    public const ParameterSignature = "/^(?'Type'\S*)\s*(?'Name'\S*)\s*(?'Description'.*)$/s";

    /**
     * Expression for parsing the '@return'-tags of DocBlocks.
     */
    public const Return = "/(?<= \* @return ).*?(?= \* @|\*\/)/s";

    /**
     * Expression for parsing the type and description of a '@return'-tag.
     */
    public const ReturnSignature = "/^(?'Type'\S*)\s*(?'Description'.*)$/s";

    /**
     * Expression for parsing the Exceptions of '@throws'-tags from DocBlocks.
     */
    public const Exceptions = "/(?<= \* @throws ).*?(?= \* @|\*\/)/s";

    /**
     * Initializes a new instance of the Method Page class.
     *
     * @param null|iterable                     $Values            Initializes the Method Page with the specified set of values.
     * @param null|iterable                     $Templates         Initializes the Method Page with the specified Collection of templates.
     * @param null|iterable                     $Stylesheets       Initializes the Method Page with the specified Collection of stylesheets.
     * @param null|iterable                     $Scripts           Initializes the Method Page with the specified Collection of scripts.
     * @param null|\Reflector                   $Reflector         Initializes the Method Page with the specified Reflector.
     * @param bool                              $Final             Initializes the Method Page with the specified flag indicating whether the Method is final.
     * @param bool                              $Abstract          Initializes the Method Page with the specified flag indicating whether the Method is abstract.
     * @param bool                              $Static            Initializes the Method Page with the specified flag indicating whether the Method is static.
     * @param null|string                       $Modifier          Initializes the Method Page with the specified modifier.
     * @param null|string                       $Name              Initializes the Method Page with the specified name.
     * @param null|\Pages\Reflect\Type          $ReturnType        Initializes the Member Page with the specified return Type.
     * @param string                            $Description       Initializes the Method Page with the specified description.
     * @param string                            $ReturnDescription Initializes the Method Page with the specified return description.
     * @param \Pages\Reflect\Method\Parameter[] $Parameters        Initializes the Method Page with the specified set of parameters.
     * @param \Pages\Reflect\Method\Exception[] $Exceptions        Initializes the Method Page with the specified set of Exceptions.
     * @param bool                              $Ignore            Initializes the Method Page with the specified flag indicating whether the Method is ignored.
     * @param bool                              $Inheritdoc        Initializes the Method Page with the specified flag indicating whether the Method is static.
     * @param bool                              $Inherited         Initializes the Method Page with the specified flag indicating whether the Method is static.
     * @param bool                              $Magic             Initializes the Method Page with the specified flag indicating whether the Method is magic.
     * @param null|string                       $Since             Initializes the Method Page with the specified @since version.
     */
    public function __construct(
        ?iterable $Values = [],
        ?iterable $Templates = ["Reflect/Method"],
        ?iterable $Stylesheets = ["Reflect/Stylesheet"],
        ?iterable $Scripts = [],
        ?\Reflector $Reflector = null,
        public bool $Final = false,
        public bool $Abstract = false,
        public bool $Static = false,
        public ?string $Modifier = null,
        public ?string $Name = null,
        public ?Type $ReturnType = null,
        //DocBlock related properties.
        public string $Description = "",
        public string $ReturnDescription = "",
        public array $Parameters = [],
        public array $Exceptions = [],
        public bool $Ignore = false,
        public bool $Inheritdoc = false,
        public bool $Inherited = false,
        public bool $Magic = false,
        public ?string $Since = null
    ) {
        parent::__construct($Values, $Templates, $Stylesheets, $Scripts, $Reflector);

        //Get flags.
        $Reflector->setAccessible(true);
        $this->Final    = $Reflector->isFinal();
        $this->Abstract = $Reflector->isAbstract();
        $this->Static   = $Reflector->isStatic();
        $this->Magic    = \in_array($Reflector->name, static::Magic, true);

        //Get modifier.
        $this->Modifier = \implode(" ", \Reflection::getModifierNames($Reflector->getModifiers()));

        $this->Name = $Reflector->name;

        //Parse description.
        $Matches = [];
        if((bool)\preg_match(static::Description, (string)$Reflector->getDocComment(), $Matches)) {
            $this->Description = \trim(\str_replace("*", "", $Matches[0]));
        }

        //Parse parameters.
        if((bool)\preg_match_all(static::Parameter, (string)$Reflector->getDocComment(), $Matches)) {
            foreach($Matches[0] as $Parameter) {
                $Parameters[] = $Parameter;
            }
        }

        //Parse since.
        if((bool)\preg_match(static::Since, (string)$Reflector->getDocComment(), $Matches)) {
            $this->Since = \trim(\str_replace(" * ", "", $Matches[0]));
        }

        //Parse ignore.
        $this->Ignore = (bool)\preg_match(static::Ignore, (string)$Reflector->getDocComment());

        //Parse inheritdoc.
        $this->Inheritdoc = (bool)\preg_match(static::Inheritdoc, (string)$Reflector->getDocComment(), $Matches);
        if($Reflector->getDeclaringClass()->getParentClass() !== false) {
            foreach($Reflector->getDeclaringClass()->getParentClass()->getMethods() as $ParentMethod) {
                if($ParentMethod->getShortName() === $Reflector->getShortName()) {
                    $this->Inherited = true;
                    break;
                }
            }
        }

        //Parse parameters.
        $Parameters = [];
        if((bool)\preg_match_all(static::Parameter, (string)$Reflector->getDocComment(), $Matches)) {
            foreach($Matches[0] as $Parameter) {
                $Parameters[] = $Parameter;
            }
        }

        // Map @param-tags to parameters
        foreach($Reflector->getParameters() as $Parameter) {
            $Signature = [];

            // Search for a corresponding '@param'-tag.
            foreach($Parameters as $ParameterSignature) {
                if(
                    \str_contains($ParameterSignature, " \${$Parameter->name} ")
                    && (bool)\preg_match(static::ParameterSignature, $ParameterSignature, $Signature)
                ) {
                    break;
                }
            }
            $this->Parameters[] = new Parameter(Reflector: $Parameter, Type: new Type(Signature: $Signature["Type"] ?? "mixed"), Description: \trim($Signature["Description"] ?? "", "* \n\r") ?? "");
        }
        //Parse return type and description.
        if((bool)\preg_match(static::Return, (string)$Reflector->getDocComment(), $Matches)) {
            if((bool)\preg_match(static::ReturnSignature, \trim($Matches[0], "* \n\r"), $Matches)) {
                $this->ReturnType        = new Type(Signature: \strlen(\trim($Matches["Type"])) > 2 ? $Matches["Type"] : (string)$Reflector?->getReturnType());
                $this->ReturnDescription = $Matches["Description"] ?? "";
            }
        } else {
            $this->ReturnType        = new Type(Signature: (string)$Reflector?->getReturnType());
            $this->ReturnDescription = "";
        }

        //Parse exceptions.
        if((bool)\preg_match_all(static::Exceptions, (string)$Reflector->getDocComment(), $Matches)) {
            foreach($Matches[0] as $Exception) {
                if((bool)\preg_match(static::ReturnSignature, $Exception, $Matches)) {
                    try {
                        $this->Exceptions[] = new Reflect\Method\Exception(
                            Reflector: new \ReflectionClass($Matches["Type"] ?? "stdClass"),
                            Name: $Matches["Type"],
                            Description: \trim($Matches["Description"] ?? "", "* \n\r")
                        );
                    } catch(\Throwable $Exception) {

                    }
                }
            }
        }
    }

}